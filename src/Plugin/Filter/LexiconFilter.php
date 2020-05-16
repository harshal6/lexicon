<?php

namespace Drupal\lexicon\Plugin\Filter;

use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\taxonomy\TermStorage;
use Drupal\Component\Utility\SafeMarkup;

/**
 * @Filter(
 *   id = "filter_lexicon",
 *   title = @Translation("Lexicon Filter"),
 *   description = @Translation("Mark Lexicon terms"),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_MARKUP_LANGUAGE,
 * )
 */
class LexiconFilter extends FilterBase {

    public function process($text, $langcode) {
//        $replace = '<span class="celebrate-filter">' . $this->t('Good Times!') . '</span>';
//        $new_text = str_replace('[celebrate]', $replace, $text);
//        return new FilterProcessResult($new_text);
        $current_term = 0;
        $term = \Drupal::routeMatch()->getParameter('taxonomy_term');
        if (!empty($route_match)) {
            $current_term = $term->id();
        }
        $text = ' ' . $text . ' ';
        $replace_mode = 'superscript';
        $link_style = 'normal';
        $vids = ['test1'];
        $terms = $this->lexicon_get_terms($vids);
        $terms_replace = array();
        if (is_array($terms)) {
            foreach ($terms as $term) {
                // If the term is equal to $current_term than skip marking that term.
                if ($current_term == $term->tid) {
                    continue;
                }
                $term_title = $term->safe_description;
                $fragment = NULL;
//                if (!empty($vids) && (variable_get('lexicon_click_option', 0) == 1)) {
                if (!empty($vids) ) {
//                    if (!variable_get('lexicon_allow_no_description', FALSE) && empty($term_title)) {
                      if ( empty($term_title)) {
                          continue;
                      }
//                    if (variable_get('lexicon_page_per_letter', FALSE)) {
                    if (FALSE) {
                    //   $linkto = variable_get('lexicon_path_' . $term->vid, 'lexicon/' . $term->vid) . '/letter_' . drupal_strtolower(drupal_substr($term->name, 0, 1));
                    }
                    else {
                        $linkto = 'lexicon/' . $term->vid;//variable_get('lexicon_path_' . $term->vid, 'lexicon/' . $term->vid);
                    }

                    // Create a valid anchor id.
                    $fragment = _lexicon_create_valid_id($term->name);

                    }






                    $replace = '<span class="celebrate-filter">' . $this->t('Good Times!') . '</span>';
        $new_text = str_replace('[celebrate]', $replace, $text);
        return new FilterProcessResult($new_text);
    }

    protected function lexicon_get_terms($vids) {
        static $got = array();
        $terms = array();

        // If the terms have not been loaded get the tree for each lexicon vocabulary.
        if (!$got) {
            foreach ($vids as $vid) {
                // Load the entire vocabulary with all entities.
                $tree = TermStorage::loadTree($vid, 0, NULL, TRUE);
                // Add extra information to each term in the tree.
                foreach ($tree as $term) {
                    lexicon_term_add_info($term, TRUE);
                    $terms[] = $term;
                }
            }
            $got = $terms;
        }
        else {
            // Use the already loaded terms.
            $terms = $got;
        }

        return $terms;
    }

    public function lexicon_term_add_info(&$term, $filter = FALSE) {
        if (!isset($term->info_added)) {
            $destination = \Drupal::service('redirect.destination');
            static $click_option, $link_related, $image_field, $synonyms_field, $page_per_letter, $show_edit, $show_desc, $show_search, $edit_voc, $access_search;
            if (!isset($click_option)) {
                $click_option = 0;//variable_get('lexicon_click_option', 0);
                $link_related = TRUE;//variable_get('lexicon_link_related', TRUE);
                $image_field = '';//variable_get('lexicon_image_field_' . $term->vid, '');
                $synonyms_field = '';//variable_get("lexicon_synonyms_field_" . $term->vid, '');
                $page_per_letter = FALSE;//variable_get('lexicon_page_per_letter', FALSE);
                $show_edit = TRUE;//variable_get('lexicon_show_edit', TRUE);
                $show_search = TRUE;//variable_get('lexicon_show_search', TRUE);
                $edit_voc = TRUE;//user_access('edit terms in ' . $term->vid);
                $access_search = TRUE;//user_access('search content');
            }
            $term->id = lexicon_create_valid_id($term->name);

            $term->safe_name = SafeMarkup::checkPlain($term->name);
            if ($filter) {
                $term->safe_description = strip_tags($term->description);
            }
            else {

                $term->safe_description = check_markup($term->description, $term->format);
            }


            // If there is an image for the term add the image information to the $term
            // object.
            if ($image_field != '') {
//                $image_field_items = field_get_items('taxonomy_term', $term, $image_field);
//                if (!empty($image_field_items)) {
//                    $term->image['uri'] = $image_field_items[0]['uri'];
//                    $term->image['alt'] = check_plain($image_field_items[0]['alt']);
//                    $term->image['title'] = check_plain($image_field_items[0]['title']);
//                }
            }

            // If there are synonyms add them to the $term object.
            if ($synonyms_field != '') {
//                $synonyms_field_items = field_get_items('taxonomy_term', $term, $synonyms_field);
//                if (!empty($synonyms_field_items)) {
//                    foreach ($synonyms_field_items as $item) {
//                        $term->synonyms[] = $item['safe_value'];
//                    }
//                }
            }
            $path = 'lexicon/test1';//variable_get('lexicon_path_' . $term->vid, 'lexicon/' . $term->vid);
            if ($page_per_letter) {
               // $term->link['path'] = $path . '/letter_' . drupal_strtolower(drupal_substr($term->name, 0, 1));
            }
            // If the Lexicon overview shows all letters on one page the link must lead
            // to the appropriate anchor.
            else {
                $term->link['path'] = $path;
            }
            $term->link['fragment'] = lexicon_create_valid_id($term->name);
            // If there are related terms add the information of each related term.
//            if ($relations = _lexicon_get_related_terms($term)) {
//                foreach ($relations as $related) {
//                    $term->related[$related->tid]['name'] = check_plain($related->name);
//                    $related_path = variable_get('lexicon_path_' . $related->vid, 'lexicon/' . $related->vid);
//                    // If the related terms have to be linked add the link information.
//                    if ($link_related) {
//                        if ($click_option == 1) {
//                            // The link has to point to the term on the Lexicon page.
//                            if ($page_per_letter) {
//                                $term->related[$related->tid]['link']['path'] = $related_path . '/letter_' . drupal_strtolower(drupal_substr($related->name, 0, 1));
//                            }
//                            // If the Lexicon overview shows all letters on one page the link
//                            // must lead to the appropriate anchor.
//                            else {
//                                $term->related[$related->tid]['link']['path'] = $related_path;
//                            }
//                            $term->related[$related->tid]['link']['fragment'] = _lexicon_create_valid_id($related->name);
//                        }
//                        else {
//                            // The link has to point to the page of the term itself.
//                            $term->related[$related->tid]['link']['path'] = 'taxonomy/term/' . $related->tid;
//                            $term->related[$related->tid]['link']['fragment'] = '';
//                        }
//                    }
//                }
//            }

            if ($show_edit && $edit_voc) {
                $term->extralinks['edit term']['name'] = t('edit term');
                $term->extralinks['edit term']['path'] = 'taxonomy/term/' . $term->tid . '/edit';
                $term->extralinks['edit term']['attributes'] = array(
                    'class' => 'lexicon-edit-term',
                    'title' => t('edit this term and definition'),
                    'query' => $destination,
                );
            }
            if ($show_search && $access_search) {
                $term->extralinks['search for term']['name'] = t('search for term');
                $term->extralinks['search for term']['path'] = 'search/node/' . $term->name;
                $term->extralinks['search for term']['attributes'] = array(
                    'class' => 'lexicon-search-term',
                    'title' => t('search for content using this term'),
                    'query' => $destination,
                );
            }

            $term->info_added = TRUE;
        }
        return $term;
    }

    protected function lexicon_create_valid_id($name){
        $allowed_chars = '-A-Za-z0-9._:';

        $id = preg_replace(array(
            '/&nbsp;|\s/',
            '/\'/',
            '/&mdash;/',
            '/&amp;/',
            '/&[a-z]+;/',
            '/[^' . $allowed_chars . ']/',
            '/^[-0-9._:]+/',
            '/__+/',
        ), array(
            // &nbsp; and spaces.
            '_',
            // apostrophe, so it makes things slightly more readable.
            '-',
            // &mdash;.
            '--',
            // &amp;.
            'and',
            // Any other entity.
            '',
            // Any character that is invalid as an ID name.
            '',
            // Any digits at the start of the name.
            '',
            // Reduce multiple underscores to just one.
            '_',
        ), strip_tags($name));

        return $id;
    }
}
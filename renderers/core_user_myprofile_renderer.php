<?php

defined('MOODLE_INTERNAL') || die();

class theme_bootstrap_core_user_myprofile_renderer extends \core_user\output\myprofile\renderer {

    /**
     * Render the whole tree.
     *
     * @param \core_user\output\myprofile\tree $tree
     *
     * @return string
     */
    public function render_tree(\core_user\output\myprofile\tree $tree) {
        $return = $this->render_key_info($tree);
        $return .= \html_writer::start_tag('div', array('class' => 'profile_tree'));
        $categories = $tree->categories;
        foreach ($categories as $category) {
            $return .= $this->render($category);
        }
        $return .= \html_writer::end_tag('div');
        return $return;
    }

    /**
     * Render some key info before the rest of the profile tree.
     *
     * @param \core_user\output\myprofile\tree $tree
     * @return string
     */
    public function render_key_info(\core_user\output\myprofile\tree $tree) {
        global $DB;
        $userid = $this->page->context->instanceid;

        $picture = $this->output->user_picture(
                $DB->get_record('user', array('id' => $userid)),
                array('size' => 128));

        // Array of the nodes from the tree that we want to display.
        // This could be a config setting?
        $contactfields = array('email', 'skypeid', 'custom_field_twitter');

        $contactinfo = '';
        foreach ($contactfields as $contactfield) {
            if (array_key_exists($contactfield, $tree->nodes)) {
                $contactinfo .= $this->render($tree->nodes[$contactfield]);
            }
        }

        $contact = \html_writer::tag('ul', $contactinfo);

        $keyinfo = $this->output->container($picture . $contact, 'profile_keyinfo');
        return $keyinfo;
    }

    /**
     * Render a category.
     *
     * @param category $category
     *
     * @return string
     */
    public function render_category(\core_user\output\myprofile\category $category) {
        $classes = $category->classes;
        if (empty($classes)) {
            $return = \html_writer::start_tag('section', array('class' => 'node_category panel panel-default'));
        } else {
            $return = \html_writer::start_tag('section', array('class' => 'node_category panel panel-default ' . $classes));
        }
        $return .= \html_writer::tag('div', $category->title, array('class' => 'panel-heading'));
        $nodes = $category->nodes;
        if (empty($nodes)) {
            // No nodes, nothing to render.
            return '';
        }
        $return .= \html_writer::start_tag('ul');
        foreach ($nodes as $node) {
            $return .= $this->render($node);
        }
        $return .= \html_writer::end_tag('ul');
        $return .= \html_writer::end_tag('section');
        return $return;
    }
}

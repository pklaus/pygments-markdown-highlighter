<?php
/*
 * Plugin Name: Pygments Markdown Highlighter
 * Plugin URI: https://github.com/pklaus/pygments-markdown-highlighter
 * Description: Given HTML formatted by Markdown, try to highlight the code blocks
 * Version: 0.1.2
 * Author: Stephen H. Gerstacker, Philipp Klaus
 * Author URI: http://blog.philippklaus.de/2011/06/a-pygments-highlighter-for-code-blocks-in-wordpress-posts-written-with-markdown-syntax/
 * License: MIT
 */

/*
 Copyright (c) 2011 Stephen H. Gerstacker

 Permission is hereby granted, free of charge, to any person obtaining a copy
 of this software and associated documentation files (the "Software"), to deal
 in the Software without restriction, including without limitation the rights
 to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 copies of the Software, and to permit persons to whom the Software is
 furnished to do so, subject to the following conditions:

 The above copyright notice and this permission notice shall be included in
 all copies or substantial portions of the Software.

 THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 THE SOFTWARE.
 */

/*** Define the plugin ***/
if (!class_exists("PygmentsMarkdownHighlighter")) {

  class PygmentsMarkdownHighlighter {

    private $command;
    private $lineno;
    private $linenos;
    private $pygmentize_found;
    private $style;
    private $styles;

    function PygmentsMarkdownHighlighter() {
      // Find pygmentize
      $this->command = get_option('pygments_markdown_highlighter_path', '');
      if (empty($this->command)) {
        $this->command = 'pygmentize';
      }

      exec($this->command, $output, $retval);
      unset($output);

      if ($retval == 0) {
        $this->pygmentize_found = TRUE;
      } else {
        $this->pygmentize_found = FALSE;
      }

      // Line numbers
      $this->linenos = array(
        'inline' => 'Inline',
        'table'  => 'Table',
        'none'  => 'None');
      $this->lineno = get_option('pygments_markdown_highlighter_lineno', 'none');

      // Setup styles
      $this->styles = array(
        'colorful' => 'Colorful',
        'default'  => 'Default',
        'emacs'    => 'Emacs',
        'friendly' => 'Friendly',
        'none'     => 'None');
      $this->register_style();
    }

    function create_settings_menu() {
      add_submenu_page('options-general.php',
        'Pygments Markdown Highlighter Options',
        'Pygments Markdown Highlighter',
        'administrator',
        __FILE__,
        array(&$this, 'settings_page'));
      add_filter('plugin_action_links', array(&$this, 'add_settings_link'), 10, 2 );
    }

    // Add Settings link to plugins
    function add_settings_link($links, $file) {
      static $this_plugin;
      if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);
      
      if ($file == $this_plugin){
        $settings_link = '<a href="options-general.php?page=pygments-markdown-highlighter/pygments-markdown-highlighter.php">'.__("Settings", "photosmash-galleries").'</a>';
        array_unshift($links, $settings_link);
      }
      return $links;
    }

    function filter_content($text) {
      return preg_replace_callback('/\<pre\>\<code\>((?:(?!\<\/code\>\<\/pre\>).)*)\<\/code\>\<\/pre\>/ms', array($this, 'pygmentize'), $text);
    }

    function pygmentize($matches) {
      // Prep the lines and lexer
      $lines = explode("\n", $matches[1]);
      $lexer = "text";

      // If a lexer is given, find it and remove the lexer line
      if (preg_match('/^:::([a-zA-Z0-9\.\+-]*)$/', $lines[0], $inner_matches)) {
        $lexer = $inner_matches[1];
        unset($lines[0]);
      }

      // Options
      $options = array();
      if (strcmp($this->style, 'none') != 0) {
        $options[] = 'style=' . $this->style;
      }

      if (strcmp($this->lineno, 'none') != 0) {
        $options[] = 'linenos=' . $this->lineno;
      }

      $options[] = 'nobackground=1';
      $opts = '-O ' . implode($options, ',');

      // Format!
      $descriptors = array(
        0 => array("pipe", "r"),
        1 => array("pipe", "w"),
        2 => array("pipe", "w")
      );

      $process = proc_open($this->command . " -f html " . $opts . " -l " . $lexer, $descriptors, $pipes);
      if (is_resource($process)) {
        // Dump the lines to the pipe
        foreach ($lines as $line) {
          fwrite($pipes[0], html_entity_decode($line));
          fwrite($pipes[0], "\n");
        }
        fclose($pipes[0]);

        // Read the formatted text
        $formatted_text = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $return_values = proc_close($process);

        return $formatted_text;
      } else {
        return "Error pygmentizing...";
      }
    }

    function pygmentize_found() {
      return $this->pygmentize_found;
    }

    function register_settings() {
      register_setting('pygments-markdown-highlighter-settings-group', 'pygments_markdown_highlighter_lineno');
      register_setting('pygments-markdown-highlighter-settings-group', 'pygments_markdown_highlighter_path');
      register_setting('pygments-markdown-highlighter-settings-group', 'pygments_markdown_highlighter_style');
    }

    function register_style() {
      wp_deregister_style('pygments');

      $this->style = get_option('pygments_markdown_highlighter_style', 'default');

      if (strcmp($this->style, 'none') != 0) {
        wp_register_style('pygments', plugins_url('stylesheets/pygments-' . $this->style . '.css', __FILE__));
      }
    }

    function settings_page() {
      $this->register_style();

      echo '<div class="wrap">';

      echo '<h2>Pygments Markdown Highlighter</h2>';

      if (!$this->pygmentize_found) {
        echo '<div class="error fade below-h2"><p><strong>The Pygmentize binary could not be found!</strong></p></div>';
      }

      echo '<form method="post" action="options.php">';

      settings_fields('pygments-markdown-highlighter-settings-group');

      echo '<table class="form-table">';

      // Line Numbers
      echo '<tr valign="top">';
      echo '<th scope="row">Print Line Numbers via</th>';
      echo '<td>';
      echo '<select name="pygments_markdown_highlighter_lineno">';

      foreach ($this->linenos as $key => $value) {
        if (strcmp($this->lineno, $key) == 0) {
          echo '<option value="' . $key . '" selected="selected">' . $value . '</option>';
        } else {
          echo '<option value="' . $key . '">' . $value . '</option>';
        }
      }

      echo '</select>';
      echo '</td>';
      echo '</tr>';

      // Style
      echo '<tr valign="top">';
      echo '<th scope="row">CSS Style for the Code Blocks</th>';
      echo '<td>';
      echo '<select name="pygments_markdown_highlighter_style">';

      foreach ($this->styles as $key => $value) {
        if (strcmp($this->style, $key) == 0) {
          echo '<option value="' . $key . '" selected="selected">' . $value . '</option>';
        } else {
          echo '<option value="' . $key . '">' . $value . '</option>';
        }
      }

      echo '</select>';
      echo '</td>';
      echo '</tr>';

      // Path
      echo '<tr valign="top">';
      echo '<th scope="row">Custom Path for the Pygments Binary</th>';
      echo '<td>';
      echo '<input type="text" name="pygments_markdown_highlighter_path" value="';
      echo get_option('pygments_markdown_highlighter_path', '');
      echo '" size="100" />';
      echo '</td>';
      echo '</tr>';

      echo '</table>';

      echo '<p class="submit">';
      echo '<input type="submit" class="button-primary button" value="Save" />';
      echo '</p>';

      echo '</form>';
      echo '</div>';
    }


  }

}


/*** Register the plugin ***/
if (class_exists("PygmentsMarkdownHighlighter")) {
  $pygments_markdown_highlighter = new PygmentsMarkdownHighlighter();
}

if (isset($pygments_markdown_highlighter)) {
  add_action('admin_init', array(&$pygments_markdown_highlighter, 'register_settings'));
  add_action('admin_menu', array(&$pygments_markdown_highlighter, 'create_settings_menu'));

  if ($pygments_markdown_highlighter->pygmentize_found()) {
    add_filter('the_content', array(&$pygments_markdown_highlighter, 'filter_content'), 900);
    add_filter('the_content_rss', array(&$pygments_markdown_highlighter, 'filter_content'), 900);
    add_filter('get_the_excerpt', array(&$pygments_markdown_highlighter, 'filter_content'), 900);

    wp_enqueue_style('pygments');
  }
}


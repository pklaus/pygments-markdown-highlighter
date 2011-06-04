## Pygments highlighter for code blocks in Wordpress posts written with Markdown Syntax

pygments-markdown-highlighter is a plugin for [WordPress][] to highlight Code blocks using the syntax highlighter [Pygments]. This way you can write your Post entries using [Markdown][] and have code highlighting for your Markdown code blocks. You can specify the language of the code block in its first line.

### Installation

First install Pygments on the system. On a Debian/Ubuntu web server this would be:

    ssh user@yourblog
    su
    apt-get update
    apt-get install python-pygments

I don't now how to install Pygments if you don't have administrative permissions on your server. If you do, please contact me so that I can update these instructions.

Then install the plugin itself. Again, we connect via ssh and download the plugin to the WordPress plugins folder using git. This way it is a repository, that can be updated easily:

    ssh user@yourblog
    cd /your/blog/folder/wp-content/plugins
    git clone git://github.com/pklaus/pygments-markdown-highlighter.git

If you don't have access to neither ssh nor git, you may still be able to upload the plugin to your server via *Add Plugin* → *Upload*.

Now activate the plugin on the plugin site http://yourblog/wp-admin/plugins.php.

### Configuration

There is a configuration site for the plugin. It's named 'Pygments Markdown Highlighter' and is found in the Settings section admin web interface of WordPress.
Alternatively go directly to
http://yourblog/wp-admin/options-general.php?page=pygments-markdown-highlighter/pygments-markdown-highlighter.php .

### Usage

To highlight code in your posts, simply add a line `:::lexername` to the first line of your code. Replace `lexername` with the lexer keyword for the language that you want to be highlighted as shown in the [List of Pygments Lexers][].

### Copyright and License

Copyright (c) 2011 Stephen H. Gerstacker, Philipp Klaus

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

### Author

* Stephen H. Gerstacker – The original author of this plugin
  * Homepage: <http://shortround.net/>
  * Source Code: <https://github.com/stack/pygments-markdown-highlighter>

* Philipp Klaus – Contributions to the documentation
  * Blog post on pygments-markdown-highlighter: <http://wp.me/p1fyOX-ZJ>
  * Source Code: <https://github.com/pklaus/pygments-markdown-highlighter>

[WordPress]: http://wordpress.org/
[Pygments]: http://pygments.org
[List of Pygments Lexers]: http://pygments.org/docs/lexers/
[Markdown]: http://daringfireball.net/projects/markdown/
[Markdown Syntax]: http://daringfireball.net/projects/markdown/syntax
[PHP Markdown Extra]: http://michelf.com/projects/php-markdown/extra/
## Pygments highlighter for code blocks in Wordpress posts written with Markdown Syntax

pygments-markdown-highlighter is a plugin for [WordPress][] to highlight code blocks using the syntax highlighter [Pygments]. This enables you to have code highlighting for your Markdown code blocks while writing your whole blog posts using [Markdown][]. You can specify the language of the code block in its first line.

### Installation

First install Pygments on the system. On a Debian/Ubuntu web server this would be the package [python-pygments](http://packages.debian.org/squeeze/python-pygments):

    ssh user@yourblog
    su
    apt-get update
    apt-get install python-pygments

I don't now how to install Pygments if you don't have administrative permissions on your server. If you do, please contact me so that I can update these instructions.

Then install the plugin itself. Again, we connect via ssh and download the plugin to the WordPress plugins folder using git. This way the plugin will be a git repository and can be updated easily using `git pull`:

    ssh user@yourblog
    cd /your/blog/folder/wp-content/plugins
    git clone git://github.com/pklaus/pygments-markdown-highlighter.git

If you don't have access to neither ssh nor git, you may still be able to upload the plugin to your server via *Add Plugin* → *Upload*.

Finally activate the plugin on the plugin overview page of your WordPress blog.

### Configuration

There is a configuration page for the plugin. It's named 'Pygments Markdown Highlighter' and can be found in the settings section on the admin area of your WordPress blog.

You can configure if you want the line numbers to be displayed inline with the code (bad for copy'n'paste or using a table.
In addition you can set the colour scheme for the highlighted code and a custom path to your Pygments binary.

### Usage

To highlight code in your posts, add a shebang styled first line `#!lexername` to your code. Replace `lexername` with the lexer keyword for the language that you want to be highlighted as shown in the [List of Pygments Lexers][].

An example for code that will be highlighted as Python source code on your blog (the first line will not be shown):

        #!python
        import platform
        print "This is Python %s." % platform.python_version()

### Upgrading

If you want to upgrade, a simple `git pull` in the plugin directory should be enough.

The first version of this plugin by Stephen H. Gerstacker used a different notation: `    :::lexername`. To convert posts from the old notation to the shebang style notation you can use the following SQL commands:

1. Search affected posts:  
   `SELECT post_content FROM wp_posts WHERE post_content LIKE "%\n    :::%";`
2. Updated affected posts:
   `UPDATE wp_posts SET post_content = REPLACE(post_content, "\n    :::","\n    #!");`

### Alternatives

* There is another WordPress plugin called **wp-markdown-syntax-highlight**
  <https://github.com/spjwebster/wp-markdown-syntax-highlight>.
  It includes the PHP-based GeSHi code highlighter and you can highlight
  Markdown code blocks with an language indicator like a shebang: `#!python`.
* Write regular Markdown code blocks and use
  **[highlight.js](http://softwaremaniacs.org/soft/highlight/en/)**,
  (a pure-javascript syntax highlighter that automatically detects the
  language of code blocks). You may install this script manually or try
  out the WordPress plugin <http://lpriori.org/highlightjs/>.  
* As you can put regular HTML in your Markdown text, you may just put
  a `<code>` block in your text.
  This way you can **rely on any other source code markup plugin** to
  markup your code.

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

* Philipp Klaus – Contributions to the documentation and the plugin.
  * Blog post on pygments-markdown-highlighter: <http://wp.me/p1fyOX-ZJ>
  * Source Code: <https://github.com/pklaus/pygments-markdown-highlighter>
  * E-Mail: philipp.klaus →AT→ gmail.com

[WordPress]: http://wordpress.org/
[Pygments]: http://pygments.org
[List of Pygments Lexers]: http://pygments.org/docs/lexers/
[Markdown]: http://daringfireball.net/projects/markdown/
[Markdown Syntax]: http://daringfireball.net/projects/markdown/syntax
[PHP Markdown Extra]: http://michelf.com/projects/php-markdown/extra/

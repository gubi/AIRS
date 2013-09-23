##What it is

Display on a certain number of characters of text, and trigger the display of the full text with an automatically appended "Read More" link.

Customizable: number of characters, ellipsis text, "Read More" link, and whether displayed text splits words

##Get it
git clone git://github.com/jtsnake/jquery-readmore.git

##How to use it
```
$( [selector] ).readmore( [options] );
```
Perform readmore on jQuery selection.
* **selector** - The selector selecting the sub-elements to perform readmore on.
* **options** - A map of additional options to pass to the method. Supported are:
    * **substr_len** - The number of chars to display. Defaults to 500.
    * **split_word** - If *true* splits at exactly **substr_len** chars even if it the split is in a word; if *false* split occurs on first space character after **substr_len** chars. Defaults to *false*.
    * **ellipses** - The ellipsis character or string to use. Defaults to '&#8230;' (ellipsis).
    * **more_link** - The link displayed after the **ellipses** string to show the remaining content. Defaults to `<a class="readm-more">Read&nbsp;More</a>`.
    * **more_clzz** - The class to use for **more_links**. Defaults to 'readm-more'.
    * **ellipse_clzz** - The ellipsis class. Defaults to 'readm-continue'.
    * **hidden_clzz** - The hidden class for the truncated text. Defaults to 'readm-hidden'.

##Demo

See demo at [jtsnake.github.com/jquery-readmore](http://jtsnake.github.com/jquery-readmore/).

##Examples
###Using default options
Code:
```
<div class="collapse">
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla id eleifend arcu. In hac habitasse platea dictumst. Aenean vestibulum scelerisque augue sit amet accumsan. Praesent sit amet interdum enim. Etiam nec volutpat metus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Proin sit amet velit sit amet mi laoreet euismod vitae eget enim. Curabitur placerat venenatis semper. Cras erat odio, rhoncus faucibus laoreet et, feugiat sit amet risus. Duis laoreet iaculis enim, a porttitor dui fermentum id. Nullam posuere hendrerit elit, in pellentesque magna rhoncus tempor.

Vestibulum vel vulputate erat. Mauris eget libero eget leo tempus fringilla. Sed ultricies accumsan metus a congue. Nam quis fermentum orci. Aliquam ultricies facilisis rutrum. Nulla gravida vulputate cursus. Integer a tempus lacus. Cras laoreet, sem id laoreet pretium, lectus nulla suscipit elit, et laoreet justo turpis at quam. Vestibulum dictum, odio quis sodales ornare, diam quam sodales libero, in gravida sem massa vel orci. Nam tempor mauris posuere lectus pulvinar a consequat lacus egestas. Sed faucibus ipsum at metus iaculis pellentesque. Sed adipiscing, tortor sed placerat tempor, libero tortor sollicitudin nisl, at egestas nibh diam eget turpis.
</div>

<script>
  $('.collapse').readmore();
</script>
```
Output:

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla id eleifend arcu. In hac habitasse platea dictumst. Aenean vestibulum scelerisque augue sit amet accumsan. Praesent sit amet interdum enim. Etiam nec volutpat metus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Proin sit amet velit sit amet mi laoreet euismod vitae eget enim. Curabitur placerat venenatis semper. Cras erat odio, rhoncus faucibus laoreet et, feugiat sit amet risus. Duis laoreet...Read More 

###Using custom options
Code:
```
<script>
  $('.collapse').readmore({ split_word: true });
</script>
```
Output:

Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla id eleifend arcu. In hac habitasse platea dictumst. Aenean vestibulum scelerisque augue sit amet accumsan. Praesent sit amet interdum enim. Etiam nec volutpat metus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Proin sit amet velit sit amet mi laoreet euismod vitae eget enim. Curabitur placerat venenatis semper. Cras erat odio, rhoncus faucibus laoreet et, feugiat sit amet risus. Duis la...Read More 
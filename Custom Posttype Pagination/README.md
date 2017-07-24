# WP-Pagination

[![License](http://img.shields.io/badge/License-MIT-blue.svg)](http://opensource.org/licenses/MIT)

### Pagination without a plugin on home and pages using custom post types and pretty permalinks

Installation
------------
Place the functions.php inside your WP main functions file
Add the index.php to your WP query where you desire pagination
For default styles, refer to the style.scss file


Detailed Explaination
-----------
**WordPress Pagination without a plugin.**

How does it work? 

* See the full write up on the [stackoverflow post](http://stackoverflow.com/questions/13768900/wordpress-pagination-not-working/13856345#13856345)

* Allows pagination on index.php as well as page and probably singles.

* Allows use with custom post types

* Allows pretty permalinks

* Default styles are available in the [style.scss](https://raw.githubusercontent.com/gst4158/WP-Pagination/master/style.scss)

* Avoid using query_post you get to stop some really funky stuff that you sometimes get when using it

* The **if ( get_query_var('paged') )** portion of the query checks if your on home, page, or single and tells the $paged variable how to react in turn.

* Do not reset the WP query after the endwhile. This takes place after the pagination function




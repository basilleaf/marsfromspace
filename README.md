Scrapes Mars Reconnaissance Orbiter (HiRISE) press release images and content from http://hirise.lpl.arizona.edu/releases/all_captions.php and publish to a Wordpress blog http://www.marsfromspace.com/about/

New: added a django + tastypie api, if anyone wants to grab all the data we scraped: https://github.com/basilleaf/marsfromspace/tree/master/api>

Salmoncream is our hacked WP theme.

Posts up to 5 a day, runs on Heroku scheduler:

heroku run python scrape_to_wordpress.py page_min page_max

ie:

heroku run python scrape_to_wordpress.py 1 5

import re
from random import randint
from time import sleep
import urllib
import urllib2
import boto
from bs4 import BeautifulSoup
import xmlrpclib
from wordpress_xmlrpc import Client, WordPressPost
from wordpress_xmlrpc.methods.posts import NewPost
from wordpress_xmlrpc.compat import xmlrpc_client
from wordpress_xmlrpc.methods import media, posts
from secrets import *

base_url = 'http://hirise.lpl.arizona.edu/releases/all_captions.php'

base_url_wallpapers = 'http://hirise.lpl.arizona.edu/'
local_img_dir = "/Users/lballard/projects/marsfromspace/images/"
published_url = "https://s3.amazonaws.com/marsfromspace/published.txt"

# get the previously published list
response = urllib2.urlopen(published_url)
previously_published = [p.rstrip() for p in response.readlines()]


def fetch_remote_file(url, local_img_dir, repeat):
    local_file = local_img_dir + url.split('/')[-1]
    try:
        print('making ' + str(local_file))
        urllib.urlretrieve( url, local_file)
        with open(local_file):
            pass
        return local_file
    except IOError:
        if repeat:  # try again..
            sleep(3)
            fetch_remote_file(url, local_img_dir, False)
        else:
            return False


def grab_all_page_urls(base_url):

    page_urls = []
    print base_url
    # construct the index page urls from base_url and page no
    for i in range(80, 70, -1):
        page_urls.append(base_url + '?page=%s' % str(i))

    all_links = []
    for url in page_urls:
        try:
            index_page = urllib2.urlopen(url).read()
        except urllib2.HTTPError:
            print('FAIL ' + url)
            continue

        try:
            index_soup = BeautifulSoup(index_page)
            all_cells = index_soup.findAll('td')  # each listing is in a table cell
            for cell in all_cells:
                all_links.append(('/').join(base_url.split('/')[:-2]) + '/' + cell.a.get('href').split('/')[1:][0])
        except:
            pass  # malformity


    return all_links

def grab_content_from_page(detail_url):

    try:
        index_page = urllib2.urlopen(detail_url).read()
    except urllib2.HTTPError:
        print('FAIL ' + detail_url )
        return False

    soup = BeautifulSoup(index_page)


    img = ''
    for l in soup.findAll('a'):
        try:
            if str(l.contents[0]) == '1280':  # designer lady wants the 1280 image, sometimes there isn't one..
                img = str(l.get('href'))
        except (IndexError, UnicodeEncodeError) as e:
            pass  # none or strange link contents no worries

    if not img:
        return False  # if we can't get the 1280 image we are passing on this page ..

    # fetch the image so we have it locally
    local_img_file = fetch_remote_file(base_url_wallpapers + img, local_img_dir, True)
    if not local_img_file:
        return False  # ccouldn't fetch remot file it, move along

    try:
        title = soup.findAll('a', {'id':'example1'})[0].get('title')
    except IndexError:
        return False  # no title no post move along

    # scrape the content and clean it up a bit..
    content = re.findall(r'<div class="caption-text">\s*(.*?)\s*<div class="social">', ' '.join((str(soup).splitlines())))[0]
    soup_content = BeautifulSoup(content)
    content = soup_content.prettify()
    content = content.replace('\n', ' ')  \
                     .replace('href="images/', 'target = "_blank" href="http://hirise.lpl.arizona.edu/images/') \
                     .replace('href="E', 'target = "_blank" href="http://hirise.lpl.arizona.edu/E') \
                     .replace('href="T', 'target = "_blank" href="http://hirise.lpl.arizona.edu/T') \
                     .replace('href="r', 'target = "_blank" href="http://hirise.lpl.arizona.edu/r') \
                     .replace('href="j', 'target = "_blank" href="http://hirise.lpl.arizona.edu/j') \
                     .replace('href="p', 'target = "_blank" href="http://hirise.lpl.arizona.edu/p') \
                     .replace('href="d', 'target = "_blank" href="http://hirise.lpl.arizona.edu/d') \
                     .replace('href="e', 'target = "_blank" href="http://hirise.lpl.arizona.edu/e') \
                     .replace('href="P', 'target = "_blank" href="http://hirise.lpl.arizona.edu/P')

    content += '<p>More info and image formats at <a href = "%s">%s</a></p>' % (detail_url, detail_url)
    content += '<p>Image: NASA/JPL/University of Arizona </p>'

    return (title, content, detail_url, local_img_file)


def log_as_published(img_id):
    # grab previously published
    previously_published.append(img_id)
    # update the published log
    s3 = boto.connect_s3(AWS_ACCESS_KEY_ID,AWS_SECRET_ACCESS_KEY)
    bucket = s3.create_bucket(BUCKET_NAME)
    key_name = published_url.split('/').pop()
    bucket.delete_key(key_name)
    key = bucket.new_key(key_name)
    key.set_contents_from_string("\n".join(previously_published))
    key.set_acl('public-read')

def post_to_wordpress(title, content, detail_url, local_img_file):
    # first upload the image
    data = {
        'name': local_img_file.split('/')[-1],
        'type': 'image/jpg',  # mimetype
    }
    wp = Client('http://www.marsfromspace.com/xmlrpc.php', WP_USER, WP_PW)

    # read the binary file and let the XMLRPC library encode it into base64
    with open(local_img_file, 'rb') as img:
        data['bits'] = xmlrpc_client.Binary(img.read())
    response = wp.call(media.UploadFile(data))
    attachment_id = response['id']

    # now post the post and the image
    post = WordPressPost()
    post.post_type = 'portfolio'
    post.title = title
    post.content = content
    post.post_status = 'publish'
    post.thumbnail = attachment_id

    if wp.call(NewPost(post)):
        img_id = local_img_file.split('/')[-1].split('.')[0]
        log_as_published(img_id)


all_links = grab_all_page_urls(base_url)
for detail_url in all_links:
    img_id = detail_url.split('/')[-1]
    if img_id not in previously_published:
        print("fetching data from " + detail_url)
        try:
            (title, content, detail_url, local_img_file) = grab_content_from_page(detail_url)
        except TypeError:
            continue  # move along

        print("posting to WP: " + title)
        post_to_wordpress(title, content, detail_url, local_img_file)

print("Bye!")
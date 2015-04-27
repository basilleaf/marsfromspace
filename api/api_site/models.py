from django.db import models

from tastypie.utils.timezone import now
from django.contrib.auth.models import User
from django.db import models
from django.template.defaultfilters import slugify

# title, content, detail_url,, img_url
class DetailPage(models.Model):
    title = models.CharField(max_length=200)
    slug = models.SlugField()
    content = models.TextField()
    detail_url = models.CharField(max_length=250, unique=True)
    img_url = models.CharField(max_length=255, null=True, blank=True)
    credit = models.CharField(max_length=30)
    
    def __unicode__(self):
        return self.title

    def save(self, *args, **kwargs):
        # For automatic slug generation.
        if not self.slug:
            self.slug = slugify(self.title)[:50]

        self.credit = 'NASA/JPL/University of Arizona'

        return super(DetailPage, self).save(*args, **kwargs)


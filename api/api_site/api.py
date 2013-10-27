# myapp/api.py
from tastypie.resources import ModelResource
from api_site.models import DetailPage
from serializers import CSVSerializer


class EntryResource(ModelResource):
    class Meta:
        queryset = DetailPage.objects.all()
        resource_name = 'entry'
        max_limit = None
        serializer = CSVSerializer(formats=['json', 'csv'])


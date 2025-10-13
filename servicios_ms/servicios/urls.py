from rest_framework import routers
from .views import ServicioViewSet

router = routers.DefaultRouter()
router.register(r'servicios', ServicioViewSet)

urlpatterns = router.urls

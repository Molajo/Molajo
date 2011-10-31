{% set content = $this->row->text %}
{% set footer = 'Molajo crocodiles rock' %}
<div id="content">{% block content %}{% endblock %}</div>
  <div id="footer">
    {% block footer %}
      &copy; Copyright 2011 by <a href="http://molajo.org/">Babs</a>.
    {% endblock %}
  </div>
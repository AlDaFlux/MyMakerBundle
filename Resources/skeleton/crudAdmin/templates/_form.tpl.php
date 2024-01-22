{{ form_start(form) }}
    {{ form_widget(form) }}
    <button class="btn btn-block btn-primary">
                        <i class='{% trans %}crud.save.icon{% endtrans %}'></i>
                        {% trans %}crud.save.text{% endtrans %}
        </button>
{{ form_end(form) }}

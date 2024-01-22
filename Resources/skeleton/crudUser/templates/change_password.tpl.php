<?php if ($extend) {?>
    {% extends "<?= $extend ?>" %}
<?php }?>
    


{% block body %}

    <h1>{{ 'title.change_password'|trans }}</h1>
    <h2>
        <i class='{% trans %}crud.edit.icon{% endtrans %}'></i> {% trans %}crud.edit.text{% endtrans %} <?= $entity_class_name ?>
    </h2>
    
        {{ form_start(form) }}
        {{ form_widget(form) }}

        <button type="submit" class="btn btn-primary">
            <i class="fa fa-save" aria-hidden="true"></i> {{ 'crud.action.save'|trans }}
        </button>
    {{ form_end(form) }}
{% endblock %}

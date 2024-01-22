<form method="post" action="{{ path('<?= $route_name ?>_delete', {'<?= $entity_identifier ?>': <?= $entity_twig_var_singular ?>.<?= $entity_identifier ?>}) }}" 
      onsubmit="return confirm('{% trans %}crud.delete.before{% endtrans %}');">
    
    <input type="hidden" name="_method" value="DELETE">
    <input type="hidden" name="_token" value="{{ csrf_token('delete' ~ <?= $entity_twig_var_singular ?>.<?= $entity_identifier ?>) }}">
    <button class="btn  btn-block  btn-danger">
        <i class='{% trans %}crud.delete.icon{% endtrans %}'></i>
        {% trans %}crud.delete.text{% endtrans %}
    </button>
</form>

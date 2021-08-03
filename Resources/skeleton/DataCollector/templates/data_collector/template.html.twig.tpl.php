{% extends '@WebProfiler/Profiler/layout.html.twig' %}

{% block toolbar %}
    {% set icon %}
        {% include('<?= $icon_path ?>') %}
        <span class="sf-toolbar-value"><?= $collector_name ?></span>
    {% endset %}

    {% set text %}
        {# this is the content displayed when hovering the mouse over  the toolbar panel #}
         
        
        <div class="sf-toolbar-info-piece">
            <b>Collector <?= $collector_name ?></b>
            <span class='sf-toolbar-status sf-toolbar-status-green'>OK</span>
        </div>
    {% endset %}

    {{ include('@WebProfiler/Profiler/toolbar_item.html.twig') }}
    
    
    
{% endblock %}

{% block menu %}

   
    <span class="label ">
        <span class="icon">
        {% include('<?= $icon_path ?>') %}
        </span>
        <strong> <?= $collector_name ?> </strong>
            <span class="count">
                <span>OK</span>
            </span>
    </span>
{% endblock %}




{% block panel %}
    <style>
        h1.error
        {
            background-color: rgb(176, 65, 62);
            color: white;
        }
    </style>
    
    <h2> <?= $collector_name ?> </h2>
   
     <div class="empty">
            <p>There are no logs .</p>
    </div>
    
       
                   
</div>
{% endblock %}

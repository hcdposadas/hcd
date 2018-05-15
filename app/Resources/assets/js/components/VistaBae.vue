<template>
    {% if sesion.asuntosEntrados %}
    {{ sesion.asuntosEntrados | raw }}
    {% else %}
    {% for titulo, lista in proyectos %}
    {% if lista.count %}
    <h3 style="text-align: center; margin-top: 5%"><u>{{ titulo }}</u></h3>
    <div class="cuerpo">

        {% for proyecto in lista %}
        <div class="proyecto">
            <p>
            <dl>
                <dt><strong>EXPTE. Nº {{ proyecto.expediente }}</strong></dt>
                <dd>{{ proyecto.expediente.extractoTemario | raw }}</dd>
            </dl>
            </p>
            <p style="text-align: center">
                {% if proyecto.expediente.giros.count > 1 %}
                A las Comisiones de
                {% else %}
                A la Comisión de
                {% endif %}

                {% if proyecto.expediente.girosOrdenados.count <= 2 %}
                {% if proyecto.expediente.girosOrdenados.count == 1 %}
                {% for giro in proyecto.expediente.girosOrdenados %}
                <strong>{{ giro.comisionDestino.abreviacion }}</strong>
                {% endfor %}
                {% else %}
                {% for giro in proyecto.expediente.girosOrdenados %}
                <strong>{{ giro.comisionDestino.abreviacion }}</strong> {% if not loop.last %} y de {% endif %}
                {% endfor %}
                {% endif %}
                {% else %}
                {% for giro in proyecto.expediente.girosOrdenados %}
                {% set ender = '; ' %}
                {% if loop.index == (loop.length - 1) %}
                {% set ender = ' y de ' %}
                {% endif %}
                <strong>{{ giro.comisionDestino.abreviacion }}</strong>{{ ender }}
                {% endfor %}
                {% endif %}

            </p>
        </div>
        {% endfor %}
    </div>
    {% endif %}
    {% endfor %}
    {% endif %}
</template>
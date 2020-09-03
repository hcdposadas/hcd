<style scoped>
#lista-pedir-palabra-wrapper.grande {
  position: fixed;
  right: 30px;
  bottom: 30px;
  width: 280px;
}
.leaveSpace {
	right: 7rem !important;
}
.grande #pedir-palabra-boton,
.grande .dropdown-menu {
  font-size: 2em;
}
.dropdown-menu {
  right: 0 !important;
  left: auto;
}
</style>
<template>
  <div>
    <div id="lista-pedir-palabra-wrapper" :class="{ leaveSpace: leaveSpace, grande: firewall !== 'admin' }">
      <div class="btn-group btn-block" :class="{ dropup: firewall !== 'admin' }">
        <button
          id="pedir-palabra-boton"
          class="btn btn-block"
          :class="cantidadPedidos > 0 ? blink : (firewall === 'admin' ? 'btn-primary' : 'btn-flat')"
          type="button"
          data-toggle="dropdown"
          :disabled="cantidadPedidos < 1"
        >
          <i class="fas fa-fw" :class="loading ? 'fa-spin fa-circle-o-notch' : 'fa-hand-paper'"></i>
          {{ cantidadPedidos }} pedido{{ cantidadPedidos !== 1 ? 's' : '' }}
        </button>
        <ul class="dropdown-menu">
          <li
            v-for="(pedido, idx) in pedidos"
            :key="pedido.id"
            @click.stop.prevent="cancelar(pedido)"
          >
            <a href="#">
              <span>{{ idx + 1 }}.</span>
              {{ pedido.nombre }}
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: [
		'leaveSpace',
		'firewall'
	],
  data() {
    return {
      loading: true,
      blink: "btn-danger"
    };
  },
  computed: {
    cantidadPedidos() {
      return this.$root.$data.palabras.length;
    },
    pedidos() {
      return this.$root.$data.palabras;
    }
  },
  methods: {
    cancelar(pedido) {
      if (this.loading || this.firewall ===  'admin') {
        return;
      }

      if (!confirm(`Cancelar el pedido de ${pedido.nombre}?`)) {
        return;
      }

      this.loading = true;
      axios
        .post(`${baseUrl}sesion/cancelar-pedir-palabra/${pedido.id}`)
        .then(response => {
          this.loading = false;
        });
    }
  },
  mounted() {
    setTimeout(() => {
      this.loading = false;
    }, 4000);
    setInterval(() => {
      this.blink = this.blink === "btn-danger" ? "btn-primary" : "btn-danger";
    }, 1000);
  }
};
</script>

<style scoped>
#pedir-palabra-wrapper {
  position: fixed;
  right: 30px;
  bottom: 30px;
  width: 280px;
}
#pedir-palabra-wrapper button {
  font-size: 2em;
}
</style>
<template>
  <div>
    <div id="pedir-palabra-wrapper">
      <div v-if="palabraPedida">Aguarde su turno <!-- ({{ palabraPedidaOrden }}&deg; en la lista) --></div>

      <button
        v-if="palabraPedida"
        class="btn btn-primary btn-block"
        :disabled="loading"
        @click="cancelar"
      >
        <i class="fas fa-fw fa-ban"></i> Cancelar pedido
      </button>
      <button v-else-if="loading" class="btn btn-primary btn-block" :disabled="loading">
        <i class="fas fa-fw fa-spin fa-circle-o-notch"></i> Aguarde...
      </button>
      <button v-else class="btn btn-primary btn-block" :disabled="loading" @click="pedir">
        <i class="fas fa-fw fa-hand-paper"></i> Pedir palabra
      </button>
    </div>
  </div>
</template>

<script>
export default {
  props: [],
  data() {
    return {
      loading: false
    };
  },
  computed: {
    palabraPedida() {
      return this.palabraPedidaOrden > 0;
    },
    palabraPedidaOrden() {
      return this.$root.$data.palabras.findIndex(c => c.id === user.id) + 1;
    }
  },
  methods: {
    pedir() {
      if (this.loading) {
        return;
      }
      this.loading = true;
      axios.post(`${baseUrl}sesion/pedir-palabra`).then(response => {
        this.loading = false;
      });
    },
    cancelar() {
      if (this.loading) {
        return;
      }
      this.loading = true;
      axios
        .post(`${baseUrl}sesion/cancelar-pedir-palabra`)
        .then(response => {
          this.loading = false;
        });
    }
  },
  mounted() {
    console.log("PedirPalabra ready.");
  }
};
</script>

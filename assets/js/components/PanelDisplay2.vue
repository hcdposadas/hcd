<style scoped lang="scss">
.panel-presentes-asistencia h1 {
  font-size: 10em;
  margin-top: 3%;
}

.panel-presentes-asistencia h2 {
  font-size: 5em;
}

.texto-resultado-concejal {
  font-size: 2em;
}

.panel-votacion-sesion-presentes-ausentes h2 {
  font-size: 7em;
}

.panel-votacion-sesion-presentes-ausentes h3 {
  font-size: 5em;
}

.panel-votacion-sesion-tiempo {
  clear: both;

  font-size: 3em;
  padding: 20px;
}

.circulo-verde {
  width: 54px;
  height: 54px;
  border-radius: 50%; /* Hace que el elemento tenga forma de círculo */
  background-color: green; /* Color verde */
}
.circulo-gris {
  width: 54px;
  height: 54px;
  border-radius: 50%; /* Hace que el elemento tenga forma de círculo */
  background-color: grey; /* Color verde */
}
.circulo-rojo {
  width: 54px;
  height: 54px;
  border-radius: 50%; /* Hace que el elemento tenga forma de círculo */
  background-color: red; /* Color verde */
}
.circulo-vacio {
  width: 56px;
  height: 56px;
  border-radius: 50%; /* Hace que el elemento tenga forma de círculo */
  border: 2px solid black;
  background-color: transparent; /* No tiene background */
}

.presentes {
  padding-top: 10px;
  padding-bottom: 10px;
  padding-left: 30px;
  padding-right: 30px;
  font-size: 2.5em;
  border-radius: 5%; /* Hace que el elemento tenga forma de círculo */
  font-weight: bold;
  color: white;
  margin-top: 15px;
  margin-bottom: 15px;
  margin-right: 10px;
  margin-left: 10px;
  background-color: #65c98a; /* No tiene background */
  display: inline-block;
}
.ausentes {
  padding-top: 10px;
  padding-bottom: 10px;
  padding-left: 30px;
  padding-right: 30px;
  font-size: 2.5em;
  border-radius: 5%; /* Hace que el elemento tenga forma de círculo */
  font-weight: bold;
  color: white;
  margin-top: 10px;
  margin-bottom: 10px;
  margin-right: 10px;
  margin-left: 10px;
  background-color: #c96565; /* No tiene background */
  display: inline-block;
}
</style>

<template>
  <div class="content-wrapper">
    <div class="content">
      <div class="container-fluid">
        <div v-if="panel === 'presentes'" class="row">
          <div class="col-lg-12">
            <div class="row panel-presentes-asistencia">
              <div class="col-lg-6 text-center">
                <h2>Presentes</h2>
                <h1>{{ quorum.presentes }}</h1>
              </div>
              <div class="col-lg-6 text-center">
                <h2>Ausentes</h2>
                <h1>{{ quorum.ausentes }}</h1>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div
                  class="alert text-center"
                  :class="[quorum.hayQuorum ? 'alert-success' : 'alert-danger']"
                >
                  <h1>
                    <span v-if="quorum.hayQuorum">Hay Quórum</span>
                    <span v-else>No Hay Quórum</span>
                  </h1>
                </div>
              </div>
            </div>
<div class="row">
              <div class="col-6">
                <span v-if="quorum.lista.includes(99)" class="presentes">
                  ALMIRON
                </span>
                <span v-if="quorum.lista.includes(82)" class="presentes">
                  ARGAÑARAZ
                </span>
                <span v-if="quorum.lista.includes(100)" class="presentes">
                  CARDOZO
                </span>
                <span v-if="quorum.lista.includes(87)" class="presentes">
                  DIB
                </span>
                <span v-if="quorum.lista.includes(96)" class="presentes">
                  GOMEZ DE OLIVEIRA
                </span>
                <span v-if="quorum.lista.includes(76)" class="presentes">
                  VIGO
                </span>
                <span v-if="quorum.lista.includes(81)" class="presentes">
                  ZARZA
                </span>
                <span v-if="quorum.lista.includes(75)" class="presentes">
                  HORIANSKI
                </span>
                <span v-if="quorum.lista.includes(78)" class="presentes">
                  MAZAL
                </span>
                <span v-if="quorum.lista.includes(103)" class="presentes">
                  FERNANDEZ
                </span>
                <span v-if="quorum.lista.includes(92)" class="presentes">
                  SALOM
                </span>
                <span v-if="quorum.lista.includes(93)" class="presentes">
                  SCROMEDA
                </span>
                <span v-if="quorum.lista.includes(97)" class="presentes">
                  TRAID
                </span>
                <span v-if="quorum.lista.includes(31)" class="presentes">
                  MARTINEZ
                </span>
              </div>

              <div class="col-6">
                <span v-if="!quorum.lista.includes(99)" class="ausentes">
                  ALMIRON
                </span>

                <span v-if="!quorum.lista.includes(82)" class="ausentes">
                  ARGAÑARAZ
                </span>

                <span v-if="!quorum.lista.includes(100)" class="ausentes">
                  CARDOZO
                </span>
                <span v-if="!quorum.lista.includes(87)" class="ausentes">
                  DIB
                </span>
                <span v-if="!quorum.lista.includes(96)" class="ausentes">
                  GOMEZ DE OLIVEIRA
                </span>
                <span v-if="!quorum.lista.includes(76)" class="ausentes">
                  VIGO
                </span>
                <span v-if="!quorum.lista.includes(81)" class="ausentes">
                  ZARZA
                </span>
                <span v-if="!quorum.lista.includes(75)" class="ausentes">
                  HORIANSKI
                </span>
                <span v-if="!quorum.lista.includes(78)" class="ausentes">
                  MAZAL
                </span>
                <span v-if="!quorum.lista.includes(103)" class="ausentes">
                  FERNANDEZ
                </span>
                <span v-if="!quorum.lista.includes(92)" class="ausentes">
                  SALOM
                </span>
                <span v-if="!quorum.lista.includes(93)" class="ausentes">
                  SCROMEDA
                </span>
                <span v-if="!quorum.lista.includes(97)" class="ausentes">
                  TRAID
                </span>
                <span v-if="!quorum.lista.includes(31)" class="ausentes">
                  MARTINEZ
                </span>
              </div>
            </div>
          </div>
        </div>

        <div v-if="panel === 'votacion'" class="text-center">
          <h1 class="panel-votacion-sesion">
            {{ sesion }}
          </h1>
          <h2 style="font-size: 5em; padding: 20px; font-weight: bold;">
            {{ mocion }}
          </h2>
          <div style="font-size: 3em;">{{ textoMocion }} {{ tipoMayoria }}</div>
          <hr />
          <!--<div style="text-align: center; font-size: 3em; width: 50%; float: left">-->
          <div class="row">
            <div class="col-lg-6 panel-votacion-sesion-presentes-ausentes">
              <h3>Presentes</h3>
              <h2>{{ quorum.presentes }}</h2>
            </div>

            <div class="col-lg-6 panel-votacion-sesion-presentes-ausentes">
              <!--<div style="text-align: center; font-size: 3em; width: 50%; float: right">-->
              <h3>Ausentes</h3>
              <h2>{{ quorum.ausentes }}</h2>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12">
              <h2 v-if="tiempo" class="panel-votacion-sesion-tiempo bg-black">
                Restan {{ tiempo ? duracion - tiempo : "" }} segundos
              </h2>
            </div>
          </div>
        </div>
        <div v-if="panel === 'resultados'" class="row text-center m-t-5">
          <div class="col-lg-12">
            <h1>{{ mocion.toUpperCase() }}</h1>

            <div class="row">
              <div class="col-12">
                <div
                  class="alert text-center"
                  :class="[
                    resultados.aprobado ? 'alert-success' : 'alert-danger',
                  ]"
                >
                  <h1>
                    <span>{{
                      resultados.aprobado ? "APROBADO" : "NO APROBADO"
                    }}</span>
                  </h1>
                </div>
              </div>
            </div>
            <!--<hr>-->
            <div class="row">
              <div class="col-4 text-center">
                <div class="col-12">
                  <h1 class="texto-resultado">
                    AFIRMATIVOS:
                  </h1>

                  <div class="row">
                    <div class="col-12">
                      <span
                        class="texto-resultado-concejal bold text-uppercase mr-1"
                        v-for="concejal in resultados.votaronPositivo"
                        >{{ concejal }}.-</span
                      >
                    </div>
                  </div>
                  <h1 class="texto-resultado">
                    NEGATIVOS:
                  </h1>
                  <div class="row">
                    <div class="col-12">
                      <span
                        class="texto-resultado-concejal bold text-uppercase"
                        v-for="concejal in resultados.votaronNegativo"
                        >{{ concejal }}.-</span
                      >
                    </div>
                  </div>

                  <h1 class="texto-resultado">
                    ABSTENCIONES:
                  </h1>
                  <div class="row">
                    <div class="col-12">
                      <span
                        class="texto-resultado-concejal bold text-uppercase"
                        v-for="concejal in resultados.seAbstuvieron"
                        >{{ concejal }}.-</span
                      >
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-8 padding-4">
                <div class="row justify-content-center" style="margin-top:5%">
                  <div class="circulo-vacio">
                    <span v-if="resultados.votaronPositivo.includes('DIB')">
                      <div class="circulo-verde"></div>
                    </span>
                    <span v-else>
                      <span v-if="resultados.votaronNegativo.includes('DIB')"
                        ><div class="circulo-rojo"></div>
                      </span>
                      <span v-else>
                        <span v-if="resultados.seAbstuvieron.includes('DIB')"
                          ><div class="circulo-gris"></div
                        ></span>
                      </span>
                    </span>
                  </div>
                </div>
                <div class="row" style="margin-top:5%">
                  <div class=""></div>
                  <div
                    class="col"
                    style="display: flex; justify-content: center; align-items: center;"
                  >
                    <div class="circulo-vacio">
                      <span v-if="resultados.votaronPositivo.includes('TRAID')">
                        <div class="circulo-verde"></div>
                      </span>
                      <span v-else>
                        <span
                          v-if="resultados.votaronNegativo.includes('TRAID')"
                          ><div class="circulo-rojo"></div>
                        </span>
                        <span v-else>
                          <span
                            v-if="resultados.seAbstuvieron.includes('TRAID')"
                            ><div class="circulo-gris"></div
                          ></span>
                        </span>
                      </span>
                    </div>
                  </div>

                  <div
                    class="col"
                    style="display: flex; justify-content: center; align-items: center;"
                  >
                    <div class="circulo-vacio">
                      <span
                        v-if="resultados.votaronPositivo.includes('VIGO')"
                      >
                        <div class="circulo-verde"></div>
                      </span>
                      <span v-else>
                        <span
                          v-if="resultados.votaronNegativo.includes('VIGO')"
                          ><div class="circulo-rojo"></div>
                        </span>
                        <span v-else>
                          <span
                            v-if="resultados.seAbstuvieron.includes('VIGO')"
                            ><div class="circulo-gris"></div
                          ></span>
                        </span>
                      </span>
                    </div>
                  </div>

                  <div
                    class="col"
                    style="display: flex; justify-content: center; align-items: center;"
                  >
                    <div class="circulo-vacio">
                      <span
                        v-if="resultados.votaronPositivo.includes('SCROMEDA')"
                      >
                        <div class="circulo-verde"></div>
                      </span>
                      <span v-else>
                        <span
                          v-if="resultados.votaronNegativo.includes('SCROMEDA')"
                          ><div class="circulo-rojo"></div>
                        </span>
                        <span v-else>
                          <span
                            v-if="resultados.seAbstuvieron.includes('SCROMEDA')"
                            ><div class="circulo-gris"></div
                          ></span>
                        </span>
                      </span>
                    </div>
                  </div>
                  <div
                    class="col"
                    style="display: flex; justify-content: center; align-items: center;"
                  >
                    <div
                      class="circulo-vacio"
                      style="background-color:#4f3872;font-weight:bold; font-size: 35px;color:white"
                    >
                      <span>D</span>
                    </div>
                  </div>
                </div>
                <div class="row" style="margin-top:5%">
                  <div
                    class="col"
                    style="display: flex; justify-content: center; align-items: center;"
                  >
                    <div class="circulo-vacio">
                      <span
                        v-if="resultados.votaronPositivo.includes('FERNANDEZ')"
                      >
                        <div class="circulo-verde"></div>
                      </span>
                      <span v-else>
                        <span
                          v-if="resultados.votaronNegativo.includes('FERNANDEZ')"
                          ><div class="circulo-rojo"></div>
                        </span>
                        <span v-else>
                          <span
                            v-if="resultados.seAbstuvieron.includes('FERNANDEZ')"
                            ><div class="circulo-gris"></div
                          ></span>
                        </span>
                      </span>
                    </div>
                  </div>

                  <div
                    class="col"
                    style="display: flex; justify-content: center; align-items: center;"
                  >
                    <div class="circulo-vacio">
                      <span
                        v-if="resultados.votaronPositivo.includes('CARDOZO')"
                      >
                        <div class="circulo-verde"></div>
                      </span>
                      <span v-else>
                        <span
                          v-if="resultados.votaronNegativo.includes('CARDOZO')"
                          ><div class="circulo-rojo"></div>
                        </span>
                        <span v-else>
                          <span
                            v-if="resultados.seAbstuvieron.includes('CARDOZO')"
                            ><div class="circulo-gris"></div
                          ></span>
                        </span>
                      </span>
                    </div>
                  </div>

                  <div
                    class="col"
                    style="display: flex; justify-content: center; align-items: center;"
                  >
                    <div class="circulo-vacio">
                      <span v-if="resultados.votaronPositivo.includes('MAZAL')">
                        <div class="circulo-verde"></div>
                      </span>
                      <span v-else>
                        <span
                          v-if="resultados.votaronNegativo.includes('MAZAL')"
                          ><div class="circulo-rojo"></div>
                        </span>
                        <span v-else>
                          <span
                            v-if="resultados.seAbstuvieron.includes('MAZAL')"
                            ><div class="circulo-gris"></div
                          ></span>
                        </span>
                      </span>
                    </div>
                  </div>

                  <div
                    class="col"
                    style="display: flex; justify-content: center; align-items: center;"
                  >
                    <div class="circulo-vacio">
                      <span v-if="resultados.votaronPositivo.includes('GOMEZ DE OLIVEIRA')">
                        <div class="circulo-verde"></div>
                      </span>
                      <span v-else>
                        <span
                          v-if="resultados.votaronNegativo.includes('GOMEZ DE OLIVEIRA')"
                          ><div class="circulo-rojo"></div>
                        </span>
                        <span v-else>
                          <span
                            v-if="resultados.seAbstuvieron.includes('GOMEZ DE OLIVEIRA')"
                            ><div class="circulo-gris"></div
                          ></span>
                        </span>
                      </span>
                    </div>
                  </div>

                  <div
                    class="col"
                    style="display: flex; justify-content: center; align-items: center;"
                  >
                    <div class="circulo-vacio">
                      <span v-if="resultados.votaronPositivo.includes('SALOM')">
                        <div class="circulo-verde"></div>
                      </span>
                      <span v-else>
                        <span
                          v-if="resultados.votaronNegativo.includes('SALOM')"
                          ><div class="circulo-rojo"></div>
                        </span>
                        <span v-else>
                          <span
                            v-if="resultados.seAbstuvieron.includes('SALOM')"
                            ><div class="circulo-gris"></div
                          ></span>
                        </span>
                      </span>
                    </div>
                  </div>
                </div>

                <div class="row" style="margin-top:5%">
                  <div
                    class="col"
                    style="display: flex; justify-content: center; align-items: center;"
                  >
                    <div class="circulo-vacio">
                      <span
                        v-if="resultados.votaronPositivo.includes('ALMIRON')"
                      >
                        <div class="circulo-verde"></div>
                      </span>
                      <span v-else>
                        <span
                          v-if="resultados.votaronNegativo.includes('ALMIRON')"
                          ><div class="circulo-rojo"></div>
                        </span>
                        <span v-else>
                          <span
                            v-if="resultados.seAbstuvieron.includes('ALMIRON')"
                            ><div class="circulo-gris"></div
                          ></span>
                        </span>
                      </span>
                    </div>
                  </div>

                  <div
                    class="col"
                    style="display: flex; justify-content: center; align-items: center;"
                  >
                    <div class="circulo-vacio">
                      <span
                        v-if="resultados.votaronPositivo.includes('HORIANSKI')"
                      >
                        <div class="circulo-verde"></div>
                      </span>
                      <span v-else>
                        <span
                          v-if="resultados.votaronNegativo.includes('HORIANSKI')"
                          ><div class="circulo-rojo"></div>
                        </span>
                        <span v-else>
                          <span
                            v-if="resultados.seAbstuvieron.includes('HORIANSKI')"
                            ><div class="circulo-gris"></div
                          ></span>
                        </span>
                      </span>
                    </div>
                  </div>

                  <div
                    class="col"
                    style="display: flex; justify-content: center; align-items: center;"
                  >
                    <div class="circulo-vacio">
                      <span v-if="resultados.votaronPositivo.includes('ZARZA')">
                        <div class="circulo-verde"></div>
                      </span>
                      <span v-else>
                        <span v-if="resultados.votaronNegativo.includes('ZARZA')"
                          ><div class="circulo-rojo"></div>
                        </span>
                        <span v-else>
                          <span v-if="resultados.seAbstuvieron.includes('ZARZA')"
                            ><div class="circulo-gris"></div
                          ></span>
                        </span>
                      </span>
                    </div>
                  </div>

                  <div
                    class="col"
                    style="display: flex; justify-content: center; align-items: center;"
                  >
                    <div class="circulo-vacio">
                      <span
                        v-if="resultados.votaronPositivo.includes('ARGANARAZ')"
                      >
                        <div class="circulo-verde"></div>
                      </span>
                      <span v-else>
                        <span
                          v-if="
                            resultados.votaronNegativo.includes('ARGANARAZ')
                          "
                          ><div class="circulo-rojo"></div>
                        </span>
                        <span v-else>
                          <span
                            v-if="
                              resultados.seAbstuvieron.includes('ARGANARAZ')
                            "
                            ><div class="circulo-gris"></div
                          ></span>
                        </span>
                      </span>
                    </div>
                  </div>

                  <div
                    class="col"
                    style="display: flex; justify-content: center; align-items: center;"
                  >
                    <div class="circulo-vacio">
                      <span
                        v-if="resultados.votaronPositivo.includes('MARTINEZ')"
                      >
                        <div class="circulo-verde"></div>
                      </span>
                      <span v-else>
                        <span
                          v-if="
                            resultados.votaronNegativo.includes('MARTINEZ')
                          "
                          ><div class="circulo-rojo"></div>
                        </span>
                        <span v-else>
                          <span
                            v-if="
                              resultados.seAbstuvieron.includes('MARTINEZ')
                            "
                            ><div class="circulo-gris"></div
                          ></span>
                        </span>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row " style="margin-top:5%">
              <div class="col text-center">
                <span style="font-size:40px;">AFIRMATIVOS</span>

                <div
                  style="width:100px; height:100px; background-color:green; display:inline-block; border-radius:0%;"
                >
                  <span
                    style="color:#fff; font-size:60px; line-height:100px;"
                    >{{ resultados.afirmativos }}</span
                  >
                </div>
              </div>
              <div class="col text-center">
                <span style="font-size:40px;">NEGATIVOS</span>
                <div
                  style="width:100px; height:100px; background-color:red; display:inline-block; border-radius:0%;"
                >
                  <span
                    style="color:#fff; font-size:60px; line-height:100px;"
                    >{{ resultados.negativos }}</span
                  >
                </div>
              </div>
              <div class="col text-center">
                <span style="font-size:40px;">ABSTENCIONES</span>
                <div
                  style="width:100px; height:100px; background-color:grey; display:inline-block; border-radius:0%;"
                >
                  <span
                    style="color:#fff; font-size:60px; line-height:100px;"
                    >{{ resultados.abstenciones }}</span
                  >
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
const io = require("socket.io-client");
// TODO aprobado en grande y primero, sacar tipo de mayoria, y numero de aprobados
//     lo que gana no mostrar nombres porcentaje
//     aprobados: total de votos,
//   abstuvo: total, detalle
export default {
  props: ["logoSrc", "ciudadName"],
  data() {
    return {
      quorum: {
        presentes: null,
        ausentes: null,
        hayQuorum: null,
        lista: [],
      },
      mocion: null,
      display: null,
      sesion: null,
      tipoMayoria: null,
      textoMocion: null,
      duracion: null,
      tiempo: null,
      resultados: {
        afirmativos: null,
        negativos: null,
        abstenciones: null,
        total: null,
        aprobado: null,
        votaronNegativo: [],
        votaronPositivo: [],
        seAbstuvieron: [],
      },
      panel: "presentes",
    };
  },
  mounted() {
    let socket = io("http://" + nodeHost + ":3000", {
      transports: ["websocket"],
      upgrade: true,
    });
    socket.on(
      "message",
      function(msg) {
        console.log(msg);
        switch (msg.type) {
          case "quorum":
            this.quorum.presentes = msg.data.quorum;
            this.quorum.ausentes = msg.data.ausentes;
            this.quorum.hayQuorum = msg.data.hayQuorum;
            this.quorum.lista = msg.data.presentes;
            break;
          case "votacion.finalizada":
            this.panel = "presentes";
            break;
          case "votacion.abierta":
            this.mocion = msg.data.mocion;
            this.display = true;
            this.sesion = msg.data.sesion;
            this.tipoMayoria = msg.data.tipoMayoria;
            this.textoMocion = msg.data.textoMocion;
            this.duracion = msg.data.duracion;
            this.tiempo = msg.data.tiempo;

            this.panel = "votacion";

            break;
          case "votacion.tick":
            this.tiempo = msg.data.tiempo;
            break;
          case "votacion.cerrada":
            break;
          case "votacion.resultados":
            this.mocion = msg.data.mocion;
            this.resultados.afirmativos = msg.data.afirmativos;
            this.resultados.negativos = msg.data.negativos;
            this.resultados.abstenciones = msg.data.abstenciones;
            this.resultados.total = msg.data.total;
            this.resultados.aprobado = msg.data.aprobado;
            this.resultados.votaronNegativo = msg.data.votaronNegativo;
            this.resultados.votaronPositivo = msg.data.votaronPositivo;
            this.resultados.seAbstuvieron = msg.data.seAbstuvieron;

            this.panel = "resultados";
            break;
        }
      }.bind(this)
    );
  },
};
</script>

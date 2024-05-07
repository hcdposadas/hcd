<style scoped lang="scss">
.panel-presentes-asistencia h1 {
  font-size: 15em;
  margin-top: 3%;
}

.panel-presentes-asistencia h2 {
  font-size: 7em;
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
                        v-if="resultados.votaronPositivo.includes('JIMENEZ')"
                      >
                        <div class="circulo-verde"></div>
                      </span>
                      <span v-else>
                        <span
                          v-if="resultados.votaronNegativo.includes('JIMENEZ')"
                          ><div class="circulo-rojo"></div>
                        </span>
                        <span v-else>
                          <span
                            v-if="resultados.seAbstuvieron.includes('JIMENEZ')"
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
                        v-if="resultados.votaronPositivo.includes('ROMERO')"
                      >
                        <div class="circulo-verde"></div>
                      </span>
                      <span v-else>
                        <span
                          v-if="resultados.votaronNegativo.includes('ROMERO')"
                          ><div class="circulo-rojo"></div>
                        </span>
                        <span v-else>
                          <span
                            v-if="resultados.seAbstuvieron.includes('ROMERO')"
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
                      <span v-if="resultados.votaronPositivo.includes('GOMEZ')">
                        <div class="circulo-verde"></div>
                      </span>
                      <span v-else>
                        <span
                          v-if="resultados.votaronNegativo.includes('GOMEZ')"
                          ><div class="circulo-rojo"></div>
                        </span>
                        <span v-else>
                          <span
                            v-if="resultados.seAbstuvieron.includes('GOMEZ')"
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
                        v-if="resultados.votaronPositivo.includes('MARTINEZ')"
                      >
                        <div class="circulo-verde"></div>
                      </span>
                      <span v-else>
                        <span
                          v-if="resultados.votaronNegativo.includes('MARTINEZ')"
                          ><div class="circulo-rojo"></div>
                        </span>
                        <span v-else>
                          <span
                            v-if="resultados.seAbstuvieron.includes('MARTINEZ')"
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
                      <span v-if="resultados.votaronPositivo.includes('KOCH')">
                        <div class="circulo-verde"></div>
                      </span>
                      <span v-else>
                        <span v-if="resultados.votaronNegativo.includes('KOCH')"
                          ><div class="circulo-rojo"></div>
                        </span>
                        <span v-else>
                          <span v-if="resultados.seAbstuvieron.includes('KOCH')"
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
                        v-if="resultados.votaronPositivo.includes('VELAZQUEZ')"
                      >
                        <div class="circulo-verde"></div>
                      </span>
                      <span v-else>
                        <span
                          v-if="
                            resultados.votaronNegativo.includes('VELAZQUEZ')
                          "
                          ><div class="circulo-rojo"></div>
                        </span>
                        <span v-else>
                          <span
                            v-if="
                              resultados.seAbstuvieron.includes('VELAZQUEZ')
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

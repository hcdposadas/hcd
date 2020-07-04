<?php
/**
 * Created by PhpStorm.
 * User: matias
 * Date: 14/07/17
 * Time: 10:44
 */

namespace App\Service;


class ReportesManager
{
    /** @var $container Container */
    private $tokenStorage;

    private $router;

    private $asset;
    private $knpsnappy;

    /* @var $em EntityManager */
    private $em;

    public function __construct($doctrine, $tokenStorage, $router, $asset, $knpsnappy)
    {
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
        $this->asset = $asset;
        $this->knpsnappy = $knpsnappy;
        $this->em = $doctrine;
    }

    public function getHeader()
    {

        $usuario = $this->tokenStorage->getToken()->getUser();

        $rutaLogo = $this->router->getContext()->getScheme() . '://' . $this->router->getContext()->getHost() .
            $this->asset->getUrl('apple-touch-icon.png');

        $fechaHoraActual = new \DateTime('now');

        $headerHtml = '<!DOCTYPE html>
                                <html>
                                <body style="margin:0; position: absolute;width: 100%;">
                                <div style="margin:0;">
                                <div style="float: left;width: 45%;" >
                                    <img style="max-width:100%; " src="' . $rutaLogo . '" />
                                </div>
                                <div style="float:left; width:33%;text-align:center; height:100% " >

                                </div>
                                <div style="float:right;width:33%; font-size:12pt">
									<span>' . $usuario . ' - ' . $fechaHoraActual->format('d-m-Y H:i:s') . '</span>
                                </div>
                               </div>

                               </body>
                               </html>';

        return $headerHtml;
    }

    public function getFooterHtml($piePrimeraLinea, $pieSegundaLinea = null)
    {


        $footerHtml = '<html><head><script>' . "
            function subst() {
              var vars={};
              var x=document.location.search.substring(1).split('&');
              for (var i in x) {var z=x[i].split('=',2);vars[z[0]] = unescape(z[1]);}
              var x=['frompage','topage','page','webpage','section','subsection','subsubsection'];
              for (var i in x) {
                var y = document.getElementsByClassName(x[i]);
                for (var j=0; j<y.length; ++j) y[j].textContent = vars[x[i]];
              }
            }" . '
            </script></head><body style="border:0; margin: 0;" onload="subst()">
            <table style="width: 100%">
              <tr>
                <td style="text-align:left;">
                    <div>
                        <p style="font-size: 8pt;"><b><i>' . $piePrimeraLinea . '</i></b><br> ' . $pieSegundaLinea . '
                        </p>
                    </div>
                </td>
                <td style="text-align:right;font-size: 8pt;">
                  Página <span class="page"></span> de <span class="topage"></span>
                </td>
              </tr>
            </table>
            </body></html>';

        return $footerHtml;
    }

    /*
     * $orientation V=vertical H=horizontal
     * $margin array con margenes
     */

    public function imprimir($html, $orientation = "V", $margin = null, $pageSize = 'A4')
    {
        $orientation = ($orientation == 'V') ? 'Portrait' : 'Landscape';

        if ($margin == null) {
            $margin = array(
                'right' => '1cm',
                'bottom' => '1cm',
            );
            if ($orientation == 'portrait') {
                $margin['top'] = '2cm';
                $margin['left'] = '2cm';
            } else {
                $margin['top'] = '4cm';
                $margin['left'] = '1cm';
            }
        }

        return $this->knpsnappy->getOutputFromHtml($html, array(
            'margin-left' => $margin['left'],
            'margin-right' => $margin['right'],
            'margin-top' => $margin['top'],
            'margin-bottom' => $margin['bottom'],
            'footer-right' => 'Pag [page] de [topage]',
            'header-html' => $this->getHeader(),
            'header-spacing' => '5',
            'page-size' => $pageSize,
            'orientation' => "$orientation",
        ));
    }


}
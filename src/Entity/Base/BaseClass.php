<?php
/**
 * Created by Santiago Semhan.
 */

namespace App\Entity\Base;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Entity\Usuario;

/**
 * Class BaseClass
 * @package App\Entity\Base
 */
abstract class BaseClass
{

	/**
	 * @var bool
	 *
	 * @ORM\Column(name="activo", type="boolean")
	 */
	protected $activo = true;

	/**
	 * @var \DateTime $fechaCreacion
	 *
	 * @Gedmo\Timestampable(on="create")
	 * @ORM\Column(name="fecha_creacion", type="datetime")
	 */
	protected $fechaCreacion;
	/**
	 * @var \DateTime $fechaActualizacion
	 *
	 * @Gedmo\Timestampable(on="update")
	 * @ORM\Column(name="fecha_actualizacion", type="datetime")
	 */
	protected $fechaActualizacion;

	/**
	 * @var Usuario $creadoPor
	 *
	 * @Gedmo\Blameable(on="create")
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\Usuario")
	 * @ORM\JoinColumn(referencedColumnName="id")
	 */
	protected $creadoPor;

	/**
	 * @var Usuario $actualizadoPor
	 *
	 * @Gedmo\Blameable(on="update")
	 *
	 * @ORM\ManyToOne(targetEntity="App\Entity\Usuario")
	 * @ORM\JoinColumn(referencedColumnName="id")
	 */
	protected $actualizadoPor;


	/**
	 * @return boolean
	 */
	public function getActivo()
	{
		return $this->activo;
	}

	/**
	 * @param boolean $activo
	 */
	public function setActivo($activo)
	{
		$this->activo = $activo;
	}

	/**
	 * @return \DateTime
	 */
	public function getFechaCreacion()
	{
		return $this->fechaCreacion;
	}


	/**
	 * @return \DateTime
	 */
	public function getFechaActualizacion()
	{
		return $this->fechaActualizacion;
	}

    /**
     * @return Usuario
     */
	public function getCreadoPor()
	{
		return $this->creadoPor;
	}

    /**
     * @return Usuario
     */
	public function getActualizadoPor()
	{
		return $this->actualizadoPor;
	}
}

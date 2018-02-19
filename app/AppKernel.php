<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
	public function registerBundles()
	{
		$bundles = [
			new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
			new Symfony\Bundle\SecurityBundle\SecurityBundle(),
			new Symfony\Bundle\TwigBundle\TwigBundle(),
			new Symfony\Bundle\MonologBundle\MonologBundle(),
			new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
			new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
			new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
			new AppBundle\AppBundle(),
			new JavierEguiluz\Bundle\EasyAdminBundle\EasyAdminBundle(),
			new Vich\UploaderBundle\VichUploaderBundle(),
			new FOS\UserBundle\FOSUserBundle(),
			new UsuariosBundle\UsuariosBundle(),
			new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
			new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
			new MesaEntradaBundle\MesaEntradaBundle(),
			new UtilBundle\UtilBundle(),
			new ThemeBundle\ThemeBundle(),
			new Knp\Bundle\MenuBundle\KnpMenuBundle(),
			new Tetranz\Select2EntityBundle\TetranzSelect2EntityBundle(),
            new Knp\Bundle\SnappyBundle\KnpSnappyBundle(),
			new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
        ];

		if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
			$bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
			$bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
			$bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
			$bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
		}

		return $bundles;
	}

	public function getRootDir()
	{
		return __DIR__;
	}

	public function getCacheDir()
	{
		return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
	}

	public function getLogDir()
	{
		return dirname(__DIR__).'/var/logs';
	}

	public function registerContainerConfiguration(LoaderInterface $loader)
	{
		$loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
	}
}



<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel {

	public function registerBundles() {
		$bundles = array(
			// Default bundles
			new Symfony\Bundle\AsseticBundle\AsseticBundle(),
			new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
			new Symfony\Bundle\MonologBundle\MonologBundle(),
			new Symfony\Bundle\SecurityBundle\SecurityBundle(),
			new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
			new Symfony\Bundle\TwigBundle\TwigBundle(),
			new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
			new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
			new JMS\AopBundle\JMSAopBundle(),
			new JMS\DiExtraBundle\JMSDiExtraBundle($this),
			new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),

			// Installed bundles
			new Application\Sonata\MediaBundle\ApplicationSonataMediaBundle(),
			new Application\Sonata\NewsBundle\ApplicationSonataNewsBundle(),
			new Application\Sonata\UserBundle\ApplicationSonataUserBundle(),
			new Euro\CoinBundle\EuroCoinBundle(),
			new Euro\ContactBundle\EuroContactBundle(),
			new Euro\PageBundle\EuroPageBundle(),
			new Euro\PrivateMessageBundle\EuroPrivateMessageBundle(),
			new FOS\UserBundle\FOSUserBundle(),
			new Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),
			new Knp\Bundle\MenuBundle\KnpMenuBundle(),
			new Ornicar\GravatarBundle\OrnicarGravatarBundle(),
			new Sonata\AdminBundle\SonataAdminBundle(),
			new Sonata\BlockBundle\SonataBlockBundle(),
			new Sonata\CacheBundle\SonataCacheBundle(),
			new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
			new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
			new Sonata\FormatterBundle\SonataFormatterBundle(),
			new Sonata\IntlBundle\SonataIntlBundle(),
			new Sonata\jQueryBundle\SonatajQueryBundle(),
			new Sonata\MediaBundle\SonataMediaBundle(),
			new Sonata\NewsBundle\SonataNewsBundle(),
			new Sonata\UserBundle\SonataUserBundle('FOSUserBundle'),
		);

		if (in_array($this->getEnvironment(), array('dev', 'test'))) {
			$bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
			$bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
			$bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
		}

		return $bundles;
	}

	public function registerContainerConfiguration(LoaderInterface $loader) {
		$loader->load(__DIR__ . '/config/config_' . $this->getEnvironment() . '.yml');
	}

}

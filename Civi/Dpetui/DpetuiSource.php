<?php

namespace Civi\Dpetui;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use CRM_Dpetui_ExtensionUtil as E;

class DpetuiSource implements CompilerPassInterface {

  public function process(ContainerBuilder $container) {
    if ($container->hasDefinition('data_processor_factory')) {
      $factoryDefinition = $container->getDefinition('data_processor_factory');
      $outputArguments = [
        'Civi\DataProcessor\FieldOutputHandler\Qrcodecheckin\QrCodeOutputHandler',
        [],
      ];
      $outputDefinition = new Definition('Civi\DataProcessor\Factory\Definition', $outputArguments);
      $factoryDefinition->addMethodCall('addOutputHandler', [
        'qr_code',
        $outputDefinition,
        E::ts('QR-code')
      ]);
    }
  }
}

<?php

namespace App\Form;

use App\Entity\Ticket;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\AreaAdministrativa;
use App\Repository\AreaAdministrativaRepository;
use App\Repository\TicketRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;

class TicketType extends AbstractType
{
    private $security;
    private $em;

    public function __construct(Security $security, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Obtener el área del usuario actual
        $user = $this->security->getUser();
        $area = null;
        
        if ($user) {
            $persona = $user->getPersona();
            if ($persona) {
                $cargos = $persona->getCargoPersona();
                if ($cargos && $cargos->count() > 0) {
                    $area = $cargos->first()->getAreaAdministrativa();
                }
            }
        }
        
        $builder
            ->add('texto',
            TextareaType::class,
            [
                'attr' => [ 'rows' => 3, 'maxlength' => 250  ],
                'label' => 'Descripción'
            ] )
            ->add('areaDestino', EntityType::class, [
                'class' => AreaAdministrativa::class,
                'query_builder' => function (AreaAdministrativaRepository $repository) {
                    $ids = [19, 32, 20, 21, 34, 5, 4, 24,7,9,22,6,2];
                    return $repository->createQueryBuilder('a')
                        ->where('a.id IN (:ids)')
                        ->setParameter('ids', $ids);
                },
                'label' => 'Área De Destino'
            ])
            ->add('adjuntoFile', VichFileType::class, [
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Eliminar archivo',
                'download_uri' => true,
                'download_label' => 'Descargar archivo',
                'asset_helper' => true,
                'label' => 'Archivo adjunto (PDF, DOC, DOCX, JPG, PNG, GIF)',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}

<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use App\Form\EventImageFormType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;

class EventCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Event::class;
    }

    public function createEntity(string $entityFqcn)
    {
        $event = new Event();
        $event->setAuthor($this->getUser());
        return $event;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            TextField::new('url'),
            TextField::new('place'),
            DateTimeField::new('date'),
            TextEditorField::new('description'),
            ImageField::new('image')
                ->setBasePath(' images/')
                ->setUploadDir('public/images')
                ->setFormType(FileUploadType::class)
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false),
            CollectionField::new('slider')
                ->setEntryType(EventImageFormType::class)
                ->onlyOnForms()
        ];
    }
}

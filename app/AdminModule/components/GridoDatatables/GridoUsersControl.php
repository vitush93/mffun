<?php

namespace App\AdminModule;

use App\Model\Entities\User;
use Grido\DataSources\Doctrine;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Control;

class GridoUsersControl extends Control
{
    /** @var EntityManager */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    public function render()
    {
        $this->template->setFile(__DIR__ . '/GridoUsers.latte');
        $this->template->render();
    }

    protected function createComponentGrido()
    {
        $grid = new \Grido\Grid();

        $grid->setModel(
            new Doctrine($this->entityManager->getDao(User::getClassName())->createQueryBuilder('a'))
        );
        $grid->translator->setLang('cs');
        $presenter = $this->presenter;

        $grid->addColumnNumber('id', 'ID')->setSortable();
        $grid->addColumnText('username', 'Uživ. jméno')
            ->setCustomRender(function ($item) use ($presenter) {
                $link = $presenter->link("Uzivatele:detail", $item->id);
                return "<a href='$link'>$item->username</a>";
            })->setSortable();
        $grid->addColumnText('role', 'Role')
            ->setCustomRender(
                function ($item) {
                    if ($item->role == 'user') {
                        return "Uživatel";
                    } else {
                        return \Nette\Utils\Strings::capitalize($item->role);
                    }
                }
            )->setSortable();

        $grid->addActionHref('edit', 'Upravit')->setIcon('pencil')->getElementPrototype()->class('ajax');
        $grid->addActionHref('delete', 'Odstranit')->setIcon('trash-o')->setConfirm('Opravdu?');

        $grid->addFilterText('username', '')->setSuggestion();
        $grid->addFilterSelect('role', '', array('' => '', 'user' => 'Uživatel', 'admin' => 'Admin', 'sklad' => 'Sklad'));

        $grid->setFilterRenderType(\Grido\Components\Filters\Filter::RENDER_INNER);

        $grid->getColumn('id')->getCellPrototype()->class = 'center';
        $grid->getColumn('role')->getCellPrototype()->class = 'center';
        $grid->getColumn('id')->getCellPrototype()->width = '6%';
        $grid->getColumn('username')->getCellPrototype()->width = '30%';
        $grid->getColumn('role')->getCellPrototype()->width = '10%';

        return $grid;
    }
}

interface IGridoUsersControlFactory
{
    /** @return GridoUsersControl */
    public function create();
}
<?php declare(strict_types=1);

namespace App\Presentation\Home;

use App\Core\AccessoryRepository;
use App\Core\CatalogRepository;
use Nette;

final class HomePresenter extends Nette\Application\UI\Presenter
{
	public function __construct(
		private readonly CatalogRepository $catalogs,
		private readonly AccessoryRepository $accessories,
	) {
		parent::__construct();
	}

	public function renderDefault(): void
	{
		$this->template->motors = $this->catalogs->findAll();
		$this->template->featured = $this->catalogs->findFeatured();
		$this->template->accessories = $this->accessories->findAll();
	}
}

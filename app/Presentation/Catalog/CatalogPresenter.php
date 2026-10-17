<?php declare(strict_types=1);

namespace App\Presentation\Catalog;

use App\Core\CatalogRepository;
use Nette;

final class CatalogPresenter extends Nette\Application\UI\Presenter
{
	public function __construct(
		private readonly CatalogRepository $catalogs,
	) {
		parent::__construct();
	}

	public function renderDefault(): void
	{
		$this->template->motors = $this->catalogs->findAll();
	}

	public function renderDetail(int $id): void
	{
		$motor = $this->catalogs->find($id);
		if ($motor === null) {
			$this->error('Motor tidak ditemukan', 404);
		}

		$this->template->motor = $motor;
	}
}

<?php declare(strict_types=1);

namespace App\Presentation\Brand;

use App\Core\BrandRepository;
use Nette;

final class BrandPresenter extends Nette\Application\UI\Presenter
{
	public function __construct(
		private readonly BrandRepository $brands,
	) {
		parent::__construct();
	}

	public function renderDefault(): void
	{
		$this->template->brands = $this->brands->findAll();
	}

	public function renderDetail(int $id): void
	{
		$brand = $this->brands->find($id);
		if ($brand === null) {
			$this->error('Brand tidak ditemukan', 404);
		}

		$this->template->brand = $brand;
	}
}

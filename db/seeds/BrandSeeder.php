<?php declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

final class BrandSeeder extends AbstractSeed
{
	public function run(): void
	{
		$existing = $this->fetchRow('SELECT COUNT(*) AS jumlah FROM brands');
		if ((int) ($existing['jumlah'] ?? 0) > 0) {
			return;
		}

		$this->table('brands')
			->insert([
				['nama' => 'Honda', 'negara' => 'Jepang'],
				['nama' => 'Yamaha', 'negara' => 'Jepang'],
				['nama' => 'Kawasaki', 'negara' => 'Jepang'],
			])
			->saveData();
	}
}

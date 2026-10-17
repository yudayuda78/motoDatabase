<?php declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

final class MotorSeeder extends AbstractSeed
{
	public function run(): void
	{
		$existing = $this->fetchRow('SELECT COUNT(*) AS jumlah FROM motors');
		if ((int) ($existing['jumlah'] ?? 0) > 0) {
			return;
		}

		$this->table('motors')
			->insert([
				[
					'nama' => 'Honda Vario 160',
					'merek' => 'Honda',
					'model' => 'Vario 160',
					'tipe' => 'Skuter',
					'harga' => 27000000,
					'featured' => false,
				],
				[
					'nama' => 'Yamaha NMAX',
					'merek' => 'Yamaha',
					'model' => 'NMAX',
					'tipe' => 'Skuter Sport',
					'harga' => 32000000,
					'featured' => false,
				],
				[
					'nama' => 'Kawasaki Ninja 250',
					'merek' => 'Kawasaki',
					'model' => 'Ninja 250',
					'tipe' => 'Sport Naked',
					'harga' => 65000000,
					'featured' => true,
				],
			])
			->saveData();
	}
}

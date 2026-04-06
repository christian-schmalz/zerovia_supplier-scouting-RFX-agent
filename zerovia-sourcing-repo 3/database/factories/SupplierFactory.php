<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    private static array $nogaCodes = [
        'C17', 'C17.21', 'C17.22', 'C20', 'C25', 'C26', 'C28',
        'G46', 'G46.7', 'H49', 'H52', 'J62', 'M71', 'N82',
    ];

    private static array $certifications = [
        'ISO 9001', 'ISO 14001', 'ISO 45001', 'ISO 50001',
        'EcoVadis Gold', 'EcoVadis Silver', 'UN Global Compact',
        'FSC', 'PEFC', 'CE', 'REACH', 'RoHS',
    ];

    private static array $cities = [
        ['city' => 'Zürich',    'country' => 'CH', 'lat' => 47.3769, 'lng' => 8.5417],
        ['city' => 'Bern',      'country' => 'CH', 'lat' => 46.9480, 'lng' => 7.4474],
        ['city' => 'Basel',     'country' => 'CH', 'lat' => 47.5596, 'lng' => 7.5886],
        ['city' => 'München',   'country' => 'DE', 'lat' => 48.1351, 'lng' => 11.5820],
        ['city' => 'Stuttgart', 'country' => 'DE', 'lat' => 48.7758, 'lng' => 9.1829],
        ['city' => 'Freiburg',  'country' => 'DE', 'lat' => 47.9990, 'lng' => 7.8421],
        ['city' => 'Wien',      'country' => 'AT', 'lat' => 48.2082, 'lng' => 16.3738],
        ['city' => 'Lyon',      'country' => 'FR', 'lat' => 45.7640, 'lng' => 4.8357],
    ];

    public function definition(): array
    {
        $location = $this->faker->randomElement(self::$cities);
        $certCount = $this->faker->numberBetween(1, 5);

        return [
            'name'           => $this->faker->company(),
            'country'        => $location['country'],
            'city'           => $location['city'],
            'lat'            => $location['lat'] + $this->faker->randomFloat(3, -0.5, 0.5),
            'lng'            => $location['lng'] + $this->faker->randomFloat(3, -0.5, 0.5),
            'esg_score'      => $this->faker->numberBetween(30, 95),
            'risk_level'     => $this->faker->randomElement(['low', 'low', 'medium', 'high']),
            'noga_codes'     => $this->faker->randomElements(self::$nogaCodes, $this->faker->numberBetween(1, 3)),
            'certifications' => $this->faker->randomElements(self::$certifications, $certCount),
            'website'        => $this->faker->domainName(),
            'email'          => 'procurement@' . $this->faker->domainName(),
            'description'    => $this->faker->sentence(12),
            'active'         => true,
        ];
    }

    public function highEsg(): static
    {
        return $this->state(['esg_score' => $this->faker->numberBetween(75, 98)]);
    }

    public function lowRisk(): static
    {
        return $this->state(['risk_level' => 'low']);
    }
}

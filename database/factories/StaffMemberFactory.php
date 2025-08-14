<?php

namespace Database\Factories;

use App\Enums\StaffType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StaffMember>
 */
class StaffMemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $staffType = $this->faker->randomElement(StaffType::cases());
        
        return [
            'name' => $this->faker->name(),
            'position' => $this->getPositionByType($staffType),
            'staff_type' => $staffType,
            'specialization' => $this->faker->randomElement([
                'Spektroskopi dan Analisis Material',
                'Optik dan Fotonikal',
                'Instrumentasi Elektronik',
                'Fisika Komputasi',
                'Biofisika',
                'Material Science',
                'Laser Physics',
                'Quantum Optics'
            ]),
            'education' => $this->faker->randomElement([
                'S3 Fisika - ITB (2020), S2 Fisika - UI (2015), S1 Fisika - USU (2013)',
                'S2 Teknik Fisika - ITS (2018), S1 Fisika - UGM (2015)',
                'S1 Fisika - UNPAD (2019)',
                'D3 Teknik Elektro - Politeknik (2017)'
            ]),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'photo_path' => 'staff/' . $this->faker->slug() . '.jpg',
            'bio' => $this->faker->paragraph(3),
            'research_interests' => $this->faker->sentence(8),
            'sort_order' => $this->faker->numberBetween(1, 100),
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
        ];
    }

    /**
     * Get a position title based on staff type
     */
    private function getPositionByType(StaffType $staffType): string
    {
        return match($staffType) {
            StaffType::KEPALA_LABORATORIUM => 'Kepala Laboratorium',
            StaffType::DOSEN => $this->faker->randomElement([
                'Senior Research Associate',
                'Research Scientist',
                'Assistant Professor',
                'Associate Professor'
            ]),
            StaffType::LABORAN => $this->faker->randomElement([
                'Lab Manager',
                'Technical Manager',
                'Asisten Laboratorium Senior',
                'Asisten Laboratorium'
            ]),
            StaffType::TEKNISI => $this->faker->randomElement([
                'Teknisi Laboratorium',
                'IT Support Specialist',
                'Electronics Technician',
                'Maintenance Technician'
            ]),
        };
    }

    /**
     * State for active staff members
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * State for inactive staff members
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * State for specific staff type
     */
    public function ofType(StaffType $staffType): static
    {
        return $this->state(fn (array $attributes) => [
            'staff_type' => $staffType,
            'position' => $this->getPositionByType($staffType),
        ]);
    }

    /**
     * State for head of laboratory
     */
    public function headOfLab(): static
    {
        return $this->ofType(StaffType::KEPALA_LABORATORIUM);
    }

    /**
     * State for lecturers/researchers
     */
    public function lecturer(): static
    {
        return $this->ofType(StaffType::DOSEN);
    }

    /**
     * State for laboratory staff
     */
    public function labStaff(): static
    {
        return $this->ofType(StaffType::LABORAN);
    }

    /**
     * State for technicians
     */
    public function technician(): static
    {
        return $this->ofType(StaffType::TEKNISI);
    }
}

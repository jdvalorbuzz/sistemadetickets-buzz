<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Departamentos comunes para una empresa grande
        $departments = [
            [
                'name' => 'Soporte Técnico',
                'code' => 'SOPORTE',
                'description' => 'Problemas técnicos, errores de sistema y asistencia con hardware/software',
                'color' => '#3498db', // Azul
                'is_active' => true,
            ],
            [
                'name' => 'Atención al Cliente',
                'code' => 'ATENCION',
                'description' => 'Consultas generales, información sobre productos y servicios',
                'color' => '#2ecc71', // Verde
                'is_active' => true,
            ],
            [
                'name' => 'Diseño y Creatividad',
                'code' => 'DISENO',
                'description' => 'Solicitudes de diseño gráfico, materiales visuales y creativos',
                'color' => '#9b59b6', // Morado
                'is_active' => true,
            ],
            [
                'name' => 'Desarrollo de Software',
                'code' => 'DEV',
                'description' => 'Nuevas funcionalidades, corrección de bugs y mejoras técnicas',
                'color' => '#e74c3c', // Rojo
                'is_active' => true,
            ],
            [
                'name' => 'Recursos Humanos',
                'code' => 'RRHH',
                'description' => 'Asuntos de personal, contrataciones y ambiente laboral',
                'color' => '#f39c12', // Naranja
                'is_active' => true,
            ],
            [
                'name' => 'Contabilidad y Finanzas',
                'code' => 'FINANZAS',
                'description' => 'Facturación, pagos y consultas financieras',
                'color' => '#16a085', // Verde azulado
                'is_active' => true,
            ],
            [
                'name' => 'Marketing',
                'code' => 'MKT',
                'description' => 'Campañas publicitarias, redes sociales y promociones',
                'color' => '#d35400', // Naranja oscuro
                'is_active' => true,
            ],
            [
                'name' => 'Operaciones',
                'code' => 'OPS',
                'description' => 'Logística, procesos internos y operativa diaria',
                'color' => '#2c3e50', // Azul oscuro
                'is_active' => true,
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}

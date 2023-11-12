<?php

namespace Tests\Feature;

use App\Data\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CollectionTest extends TestCase
{
    public function testCollection()
    {
        $dataCollection = collect([1, 2, 3]);

        $this->assertEqualsCanonicalizing([1, 2, 3], $dataCollection->all());
    }

    public function testPushAndPop()
    {
        $collect = collect([]);
        $collect->push(1, 2, 3);

        $this->assertEqualsCanonicalizing([1, 2, 3], $collect->all());

        $result = $collect->pop();

        $this->assertEquals(3, $result);
        $this->assertEqualsCanonicalizing([1, 2], $collect->all());

        $collect->prepend(8);
        $this->assertEqualsCanonicalizing([8, 1, 2], $collect->all());

        $ambil8 = $collect->pull(0);
        $this->assertEquals(8, $ambil8);
        $this->assertEqualsCanonicalizing([1, 2], $collect->all());
    }

    public function testCollectionForEach()
    {
        $dataCollection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        foreach ($dataCollection as $index => $value) {
            $this->assertEquals($index + 1, $value);
        }
    }

    public function testCollectionMap()
    {
        $dataCollection = collect([1, 2, 3, 4, 5]);

        $result = $dataCollection->map(function ($item) {
            return $item * 2;
        });

        $this->assertEqualsCanonicalizing([2, 4, 6, 8, 10], $result->all());
    }

    public function testMapInto()
    {
        $collection = collect(['Ardi']);

        $result = $collection->mapInto(Person::class);

        $this->assertEqualsCanonicalizing([new Person('Ardi')], $result->all());
    }

    public function testMapSpread()
    {
        $collection = collect([
            ['Ardiansyah', 'Putra'],
            ['Ardi', 'Story']
        ]);

        $result = $collection->mapSpread(function ($firstName, $lastName) {
            $fullName = "{$firstName} {$lastName}";

            return new Person($fullName);
        });

        $this->assertEquals([
            new Person('Ardiansyah Putra'),
            new Person('Ardi Story')
        ], $result->all());
    }

    public function testMapToGroups()
    {
        $collection = collect([
            [
                "nama" => "Ardi",
                "departemen" => "IT"
            ],
            [
                "nama" => "Putra",
                "departemen" => "IT"
            ],
            [
                "nama" => "Yansah",
                "departemen" => "HR"
            ]
        ]);

        $result = $collection->mapToGroups(function ($item) {
            return [
                $item['departemen'] => $item['nama']
            ];
        });

        $this->assertEqualsCanonicalizing([
            'IT' => collect(['Ardi', 'Putra']),
            'HR' => collect(['Yansah'])
        ], $result->all());


        $data = $result->all();

        $this->assertEquals($data['IT'], collect(['Ardi', 'Putra']));
    }

    public function testConcat()
    {
        $collection1 = collect([1, 2, 3]);
        $collection2 = collect([4, 5, 6]);

        $result = $collection1->concat($collection2);

        $this->assertEqualsCanonicalizing([1, 2, 3, 4, 5, 6], $result->all());
    }

    public function testCombine()
    {
        $index = collect(['nomor_1', 'nomor_2']);
        $value = collect([true, false]);

        $result = $index->combine($value);

        $this->assertEqualsCanonicalizing([
            'nomor_1' => true,
            'nomor_2' => false
        ], $result->all());
    }

    public function testCollapse()
    {
        $collection = collect([
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9]
        ]);

        $result = $collection->collapse();

        $this->assertEqualsCanonicalizing([1, 2, 3, 4, 5, 6, 7, 8, 9], $result->all());
    }

    public function testFlatMap()
    {
        $collection = collect([
            [
                'nama' => 'ardi',
                'hobi' => ['coding', 'gaming']
            ],
            [
                'nama' => 'putra',
                'hobi' => ['reading', 'writing']
            ]
        ]);

        $result = $collection->flatMap(function ($item) {
            return $item['hobi'];
        });

        $this->assertEqualsCanonicalizing([
            'coding',
            'gaming',
            'reading',
            'writing'
        ], $result->all());
    }

    public function testJoin()
    {
        $collection = collect(['ardi', 'putra', 'ardistory']);
        $this->assertEquals('ardi-putra atau ardistory', $collection->join('-', ' atau '));
    }

    public function testFilter()
    {
        $collection = collect([
            'ardi' => 90,
            'putra' => 80,
            'yansah' => 70
        ]);

        $result = $collection->filter(function ($value, $key) {
            return $value >= 80;
        });

        $this->assertEqualsCanonicalizing([
            'ardi' => 90,
            'putra' => 80
        ], $result->all());
    }

    public function testPartition()
    {
        $collection = collect([
            'ardi' => 90,
            'putra' => 80,
            'yansah' => 70
        ]);

        [$result1, $result2] = $collection->partition(function ($value, $key) {
            return $value >= 80;
        });

        $this->assertEqualsCanonicalizing([
            'ardi' => 90,
            'putra' => 80
        ], $result1->all());

        $this->assertEqualsCanonicalizing([
            'yansah' => 70
        ], $result2->all());
    }

    public function testTesting()
    {
        $collection = collect(['ardi', 'story']);

        $this->assertTrue($collection->contains('ardi'));
        $this->assertTrue($collection->contains(function ($value, $key) {
            return $value == 'story';
        }));
    }

    public function testGroupBy()
    {
        $collection = collect([
            [
                "nama" => "Ardi",
                "departemen" => "IT"
            ],
            [
                "nama" => "Putra",
                "departemen" => "IT"
            ],
            [
                "nama" => "Yansah",
                "departemen" => "HR"
            ]
        ]);

        $result = $collection->groupBy('departemen');

        $this->assertEqualsCanonicalizing([
            'IT' => collect([
                [
                    "nama" => "Ardi",
                    "departemen" => "IT"
                ],
                [
                    "nama" => "Putra",
                    "departemen" => "IT"
                ]
            ]),
            'HR' => collect([
                [
                    "nama" => "Yansah",
                    "departemen" => "HR"
                ]
            ])
        ], $result->all());
    }

    public function testSlicing()
    {
        $collection = collect([1, 2, 3, 4, 5]);
        $result1 = $collection->slice(1);

        $this->assertEqualsCanonicalizing([2, 3, 4, 5], $result1->all());

        $result2 = $collection->slice(2, 2);

        $this->assertEqualsCanonicalizing([3, 4], $result2->all());
    }

    public function testTake()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        $result1 = $collection->take(3);

        $this->assertEqualsCanonicalizing([1, 2, 3], $result1->all());

        $result2 = $collection->takeUntil(function ($item) {
            return $item == 3;
        });

        $this->assertEqualsCanonicalizing([1, 2], $result2->all());

        $result3 = $collection->takeWhile(function ($item) {
            return $item < 3;
        });

        $this->assertEqualsCanonicalizing([1, 2], $result3->all());
    }

    public function testSkip()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        $result1 = $collection->skip(3);

        $this->assertEqualsCanonicalizing([4, 5, 6, 7, 8, 9], $result1->all());

        $result2 = $collection->skipUntil(function ($item) {
            return $item == 3;
        });

        $this->assertEqualsCanonicalizing([3, 4, 5, 6, 7, 8, 9], $result2->all());

        $result3 = $collection->skipWhile(function ($item) {
            return $item < 3;
        });

        $this->assertEqualsCanonicalizing([3, 4, 5, 6, 7, 8, 9], $result3->all());
    }

    public function testChunk()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
        $result = $collection->chunk(3);

        $this->assertEqualsCanonicalizing([1, 2, 3], $result->all()[0]->all());
        $this->assertEqualsCanonicalizing([4, 5, 6], $result->all()[1]->all());
        $this->assertEqualsCanonicalizing([7, 8, 9], $result->all()[2]->all());
        $this->assertEqualsCanonicalizing([10], $result->all()[3]->all());
    }

    public function testFirst()
    {
        $collection = collect([1, 2, 3, 4, 5]);
        $result = $collection->first(function ($value, $key) {
            return $value > 3;
        });

        $this->assertEqualsCanonicalizing(4, $result);
    }

    public function testLast()
    {
        $collection = collect([1, 2, 3, 4, 5]);
        $result = $collection->last(function ($value, $key) {
            return $value < 3;
        });

        $this->assertEqualsCanonicalizing(2, $result);
    }

    public function testRandom()
    {
        $collection = collect([1, 2, 3, 4, 5, 6, 7, 8, 9]);
        $result1 = $collection->random();

        $this->assertTrue(in_array($result1, $collection->all()));
    }

    public function testExisting()
    {
        $collection = collect([1, 2, 3]);

        $this->assertFalse($collection->isEmpty());
        $this->assertTrue($collection->isNotEmpty());
        $this->assertTrue($collection->contains(2));
        $this->assertFalse($collection->contains(5));
    }
}


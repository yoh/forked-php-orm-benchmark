# PHP ORM Benchmark

## 1. Methodology

I have created this test suite to test both the performance and DX for some of the ORMs that are out there. 

I have chosen a few tests that are not trivial (eg: inserting a single row) on a not-to-simple database structure:

1. **products**. Products belong to one category (many-to-one), have many morphed images (see table description below) and belongs to many tags (many-to-many)
2. **categories**
3. **tags**
5. **images**: this table is created to hold images for multiple type of entities via it's `imageable_type` column

The benchmark runs 500 times (a number that you can change in the `AbstractTestSuite` class) for the following operations:

1. **Insert** a product along with its related category, one image and 2 tags
2. **Update** a product that has its name changed, its category's name changed, its image's path changed and the name of one of the tags changed
3. **Find** a product by its ID
4. Run a **complex query** that counts the number of products joined with categories where there are some conditions on both the `products` and the `categories` table
5. Load 10 products and **eager-load** the related category, images and tags

**Note!** Previous version of the benchmark included PDO as a reference but I removed it since the operations have become more complex.

**Disclaimer!** I am also the author of the Sirius ORM.

## 2. Requirements

* linux or bsd system
* PHP 7.2 or greater
* pdo_sqlite

#### Running All The Benchmarks

    > cd /path/to/php-orm-benchmark
    > php TestRunner.php

#### Running One Of The Benchmarks

    > cd /path/to/atlas_21
    > php TestRunner.php

## 3. Results

Smaller score is the better (i. e. the faster).

(updated 2020-February-23)

| Library                          | Insert | Update | Find   | Complex| EagerL. | memory usage|  time  |
| --------------------------------:| ------:| ------:| ------:| ------:| -------:| -----------:| ------:|
|                         AtlasOrm |   1609 |   1120 |    212 |    200 |    7866 |   7,923,952 |  11.07 |
|                         CycleOrm |   2576 |   1647 |    397 |    400 |    6556 |  10,001,720 |  11.71 |
|      CycleOrmWithGeneratedMapper |   2282 |   1585 |    247 |    504 |    5532 |  12,095,688 |  10.42 |
|            CycleOrmDynamicSchema |   2538 |   1842 |    252 |    477 |    6041 |   9,968,264 |  11.27 |
|                        DoctrineM |  17594 |  35030 |    670 |    237 |    7161 |  12,582,912 |  61.10 |
|               DoctrineMWithCache |  18442 |  34040 |    574 |    272 |    7335 |  12,582,912 |  61.04 |
|                         Eloquent |   3388 |   2438 |    518 |    781 |    9581 |   4,194,304 |  16.82 |
|             EloquentWithoutEvent |   3141 |   2155 |    435 |    709 |    8657 |   4,194,304 |  15.21 |
|                        SiriusOrm |   1797 |   1413 |    308 |    235 |    7963 |   3,727,744 |  11.78 |




For running benchmarks using the Docker shell see [.docker-stack/README.md](./.docker-stack/README.md)

Comments/things to mention about the results. Please understand I'm not proficient in most of the libraries in this test so I might have missed something.

#### Atlas ORM
- As implemented in this test, Atlas is not a full-blown Data Mapper in the sense that it does not generate domain entities but internal objects called `Record` and `RecordSet`. 
  To make it generate domain entities one needs to use `Atlas\Transit`. However the `Record` objects provided by Atlas can be easily augmented to improve their functionality enough for most applications
- **Bug 1**: doesn't save the second tag associated with a product
- **Bug 2**: the `persist()` method doesn't work as advertised for new Records so I had to use INSERTs
- This ORM stores the rows retrieved in an Storage object and fetching by ID doesn't necessarily executes the SQL so I had to alter the source code to by-pass the storage (`Mapper` line 63)
- The memory consumption increases with the number of runs (500 runs consume twice as much memory as 100 runs)

#### Cycle ORM
- stable memory consumption (no difference between 100 runs and 500 runs)

#### Doctrine ORM
- the numbers are skewed on the "find" test because I had to set all relations as eager-loaded so that the "relations" test work as the rest. I would expect lower numbers for find
- since Doctrine uses an Entity Manager I had to do a lot of `$em->clear()` to make it for a level playing field
- I have a hunch that the eager loading uses some caching and the numbers are skewed in Doctrine's favour.
- The memory consumption increases with the number of runs (500 runs consume 50% more memory than 100 runs)

#### Eloquent
- stable memory consumption (no difference between 100 runs and 500 runs)

#### Sirius ORM
- stable memory consumption (no difference between 100 runs and 500 runs)
- as stated above, I'm the author. However the library doesn't have cache-ing mechanism or any other performance enhancement options (eg: generating Proxy classes). It also doesn't have an event manager or an entity manager. I would say that in
 terms of capabilities is closest to Eloquent (the version without events).

## 4. Contributions

If you are the developer or user of an ORM, data mapper, Active Record library and you want to have it included in this repo, please send a pull-request.

If you see something wrong about the current usage of one of the libraries, please send a pull-request along with a short explanation of what that change does.

Please, try to implement the solutions using the most common configuration. If you want to include an optimized version of the test, create another one in the same folder, like that in the Cycle Orm folder.
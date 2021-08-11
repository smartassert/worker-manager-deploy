<?php

namespace App\Model;

use App\Model\InstanceMatcher\InstanceMatcherInterface;
use App\Model\InstanceSorter\InstanceCreatedDateSorter;
use App\Model\InstanceSorter\InstanceSorterInterface;

/**
 * @implements \IteratorAggregate<Instance>
 */
class InstanceCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var Instance[]
     */
    private array $instances;

    /**
     * @param Instance[] $instances
     */
    public function __construct(array $instances)
    {
        $this->instances = array_filter($instances, function ($item) {
            return $item instanceof Instance;
        });
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->instances);
    }

    public function count(): int
    {
        return count($this->instances);
    }

    public function getFirst(): ?Instance
    {
        return 0 === count($this)
            ? null
            : $this->instances[0];
    }

    public function getNewest(): ?Instance
    {
        $sortedCollection = $this->sortByCreatedDate();

        return $sortedCollection->getFirst();
    }

    public function sort(InstanceSorterInterface $sorter): self
    {
        $instances = $this->instances;

        usort($instances, function (Instance $a, Instance $b) use ($sorter): int {
            return $sorter->sort($a, $b);
        });

        return new InstanceCollection($instances);
    }

    public function filter(InstanceMatcherInterface $filter): self
    {
        $instances = [];

        foreach ($this as $instance) {
            if ($filter->matches($instance)) {
                $instances[] = $instance;
            }
        }

        return new InstanceCollection($instances);
    }

    private function sortByCreatedDate(): self
    {
        return $this->sort(new InstanceCreatedDateSorter());
    }
}

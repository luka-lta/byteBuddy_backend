<?php

declare(strict_types=1);

namespace ByteBuddyApi\Value;

final class Command
{
    private function __construct(
        private readonly ?int $id,
        private readonly string $name,
        private readonly string $description,
        private readonly bool $disabled,
    ) {
    }

    public static function from(int $id, string $name, string $descrption, bool $disabled): self
    {
        return new self($id, $name, $descrption, $disabled);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            null,
            $data['name'],
            $data['description'],
            $data['disabled']
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }
}

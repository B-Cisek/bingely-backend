# Architektura Projektu Bingely Backend

## Spis Treści
1. [Wprowadzenie](#wprowadzenie)
2. [Filozofia Architektury](#filozofia-architektury)
3. [Struktura Modułowa](#struktura-modułowa)
4. [Architektura Warstwowa](#architektura-warstwowa)
5. [Command Query Separation (CQS)](#command-query-separation-cqs)
6. [Zasady i Konwencje](#zasady-i-konwencje)
7. [Wzorce Projektowe](#wzorce-projektowe)
8. [Problemy do Naprawy](#problemy-do-naprawy)
9. [Potencjalne Ulepszenia](#potencjalne-ulepszenia)

---

## Wprowadzenie

Bingely Backend to aplikacja oparta na Symfony 7.3 i PHP 8.4, która wykorzystuje modularną architekturę warstwową z separacją Command-Query (CQS).

**Kluczowe założenia:**
- **Modułowość** - Kod organizowany w niezależne moduły biznesowe
- **Pragmatyzm** - Używanie encji Doctrine bezpośrednio, bez nadmiernej abstrakcji
- **CQS** - Rozdzielenie operacji zapisu (Commands) od odczytu (Queries)
- **Minimalne zależności** - Moduły powinny być jak najbardziej niezależne

---

## Filozofia Architektury

### Pragmatyczne Podejście

Projekt stosuje **pragmatyczne podejście do architektury**, które oznacza:

✅ **TAK:**
- Używamy encji Doctrine bezpośrednio w warstwie Domain
- Nie tworzymy osobnych klas dla obiektów wartości tam gdzie wystarczą typy skalarne
- Używamy atrybutów Doctrine w encjach
- Korzystamy z narzędzi frameworka (Symfony) bezpośrednio w infrastrukturze
- Stosujemy wzorce architektoniczne tylko tam gdzie przynoszą rzeczywistą wartość

❌ **NIE:**
- Nie tworzymy dodatkowej warstwy abstrakcji dla encji (np. osobne modele domenowe i ORM)
- Nie używamy nadmiernej abstrakcji tam gdzie nie jest potrzebna
- Nie implementujemy złożonych wzorców tam gdzie prostsza implementacja wystarczy

### Zasada Minimum Zależności

Każdy moduł powinien:
- **Być samowystarczalny** - Zawierać całą logikę biznesową swojej domeny
- **Minimalizować zależności** - Nie zależeć od innych modułów biznesowych
- **Komunikować się przez eventy** - Jeśli potrzebna jest współpraca między modułami
- **Używać Shared** - Tylko dla wspólnych narzędzi infrastrukturalnych

---

## Struktura Modułowa

### Obecne Moduły

```
src/
├── Shared/          # Infrastruktura wspólna dla wszystkich modułów
├── User/            # Moduł zarządzania użytkownikami
└── TvShow/          # Moduł katalogowy seriali TV
```

### Shared - Moduł Infrastrukturalny

**Odpowiedzialność:** Wspólne narzędzia i abstrakcje używane przez wszystkie moduły.

**Zawartość:**
- `Application/Command/` - Interfejsy i busa dla Commands (Sync/Async)
- `Domain/Entity/` - BaseEntity z UUID v7
- `Domain/Exception/` - Bazowe wyjątki domenowe
- `Infrastructure/` - Listenery, helpery API, konfiguracja Symfony

**Zasady:**
- ✅ Może być używany przez wszystkie moduły
- ✅ Zawiera tylko kod infrastrukturalny i wspólne abstrakcje
- ❌ NIE może zależeć od modułów biznesowych (User, TvShow)
- ❌ NIE powinien zawierać logiki biznesowej

### User - Moduł Użytkowników

**Odpowiedzialność:** Zarządzanie użytkownikami, autoryzacja, rejestracja.

**Główne komponenty:**
- **Entity:** `User`, `RefreshToken`
- **Commands:** `RegisterUser`
- **Queries:** `UserExistsByEmail`, `UserExistsByUsername`
- **Events:** `UserRegistered`
- **Exceptions:** `EmailAlreadyExistsException`, `UsernameAlreadyExistsException`

**Zależności:**
- ✅ Shared (infrastruktura)
- ✅ Symfony Security, LexikJWT (autoryzacja)
- ❌ Żaden inny moduł biznesowy

### TvShow - Moduł Seriali TV

**Odpowiedzialność:** Katalog seriali, integracja z TMDB API, zarządzanie gatunkami.

**Główne komponenty:**
- **Entity:** `TvShow`, `TvShowGenre`, `TvShowTranslation`
- **Commands:** `FetchTvShowGenresCommand`
- **Provider:** `TvShowProviderInterface` (abstrakcja zewnętrznych źródeł)
- **Infrastructure:** Klient TMDB, Transformery, Filtry

**Zależności:**
- ✅ Shared (infrastruktura)
- ✅ HTTP Client (integracja TMDB)
- ❌ Żaden inny moduł biznesowy

### Reguły Tworzenia Nowych Modułów

Kiedy tworzyć nowy moduł?
- ✅ Gdy identyfikujemy nowy **obszar biznesowy** z odrębną odpowiedzialnością
- ✅ Gdy funkcjonalność ma wyraźnie **odrębną domenę biznesową**
- ✅ Gdy moduł może być **rozwijany niezależnie**

Jak nazwać moduł?
- ✅ Nazwa biznesowa (np. `User`, `TvShow`, `Subscription`)
- ❌ Nie nazwa techniczna (np. `Api`, `Database`)

---

## Architektura Warstwowa

Każdy moduł powinien być zorganizowany w warstwy zgodnie z **clean architecture**:

```
Module/
├── Domain/              # Warstwa domenowa - rdzeń biznesu
│   ├── Entity/          # Encje Doctrine
│   ├── Repository/      # Interfejsy repozytoriów
│   ├── Event/           # Eventy domenowe
│   ├── Exception/       # Wyjątki domenowe
│   └── Enum/            # Enumy (typy wyliczeniowe)
├── Application/         # Warstwa aplikacyjna - use cases
│   ├── Command/         # Commands i Handlers (zapis)
│   ├── Query/           # Query interfaces (odczyt)
│   ├── Dto/             # Data Transfer Objects
│   ├── Factory/         # Fabryki tworzące encje
│   └── Provider/        # Interfejsy dostawców danych
├── Infrastructure/      # Warstwa infrastruktury - implementacja
│   └── Doctrine/        # Implementacje Doctrine
│       ├── Repository/  # Implementacje repozytoriów
│       └── Query/       # Implementacje queries
└── UserInterface/       # Warstwa interfejsu użytkownika
    ├── Controller/      # Kontrolery HTTP
    ├── Request/         # Request DTOs z walidacją
    └── Command/         # Symfony Console Commands
```

### Zasady Zależności Między Warstwami

**Reguła przepływu zależności:** Zależności mogą płynąć tylko w dół:
```
UserInterface → Application → Domain ← Infrastructure
```

#### Domain (Warstwa Domenowa)
- ✅ NIE zależy od żadnej innej warstwy
- ✅ Zawiera interfejsy repozytoriów (implementowane w Infrastructure)
- ✅ Zawiera encje Doctrine (pragmatyczne podejście)
- ✅ Zawiera wyjątki domenowe
- ✅ Zawiera eventy domenowe
- ❌ NIE może używać Symfony, Doctrine repositories, HTTP, itp.

#### Application (Warstwa Aplikacyjna)
- ✅ Zależy od Domain
- ✅ Zawiera Commands i Query interfaces
- ✅ Zawiera Handlery, Fabryki, DTOs
- ✅ Definiuje interfejsy Provider (np. `TvShowProviderInterface`)
- ❌ NIE może zależeć od Infrastructure
- ❌ NIE może zależeć od UserInterface

#### Infrastructure (Warstwa Infrastruktury)
- ✅ Zależy od Domain i Application
- ✅ Implementuje interfejsy z Domain (repozytoria)
- ✅ Implementuje interfejsy z Application (queries, providers)
- ✅ Używa Doctrine, HTTP Client, zewnętrznych API
- ❌ NIE może być używana przez Domain ani Application (tylko interfejsy)

#### UserInterface (Warstwa Interfejsu)
- ✅ Zależy od Application i Domain
- ✅ Zawiera kontrolery HTTP, CLI commands
- ✅ Zawiera Request DTOs z walidacją
- ✅ Używa Command Bus do wywoływania use cases
- ❌ NIE powinna zawierać logiki biznesowej

---

## Command Query Separation (CQS)

Projekt implementuje **CQS/CQRS** - rozdzielenie operacji zapisu (Commands) od odczytu (Queries).

### Commands - Operacje Zapisu

**Commands** reprezentują intencję zmiany stanu systemu.

#### Struktura Command

```php
namespace Bingely\{Module}\Application\Command\Sync;

use Bingely\Shared\Application\Command\Sync\Command;

final readonly class SomeCommand implements Command
{
    public function __construct(
        public string $param1,
        public int $param2,
    ) {}
}
```

**Zasady:**
- ✅ Command to **readonly DTO** z publicznymi propertiami
- ✅ Implementuje `Command` marker interface (Sync lub Async)
- ✅ Nazwa: czasownik + rzeczownik (np. `RegisterUser`, `FetchTvShowGenres`)
- ✅ Znajduje się w `Application/Command/{Sync|Async}/`
- ❌ NIE zawiera logiki biznesowej
- ❌ NIE zwraca wartości (void w handlerze)

#### Command Handler

```php
namespace Bingely\{Module}\Application\Command\Sync;

use Bingely\Shared\Application\Command\Sync\CommandHandler;

final readonly class SomeCommandHandler implements CommandHandler
{
    public function __construct(
        private SomeDependency $dependency,
    ) {}

    public function __invoke(SomeCommand $command): void
    {
        // 1. Walidacja biznesowa
        // 2. Utworzenie/modyfikacja encji
        // 3. Zapis do repozytorium
        // 4. Wysłanie eventów domenowych
    }
}
```

**Zasady:**
- ✅ Handler to **readonly klasa** z metodą `__invoke()`
- ✅ Implementuje `CommandHandler` marker interface
- ✅ Nazwa: `{CommandName}Handler`
- ✅ Znajduje się w tym samym katalogu co Command
- ✅ Autowiring zależności przez konstruktor
- ✅ Zwraca `void` - Commands nie zwracają danych
- ❌ NIE może wywoływać innych handlerów bezpośrednio

#### Sync vs Async Commands

**Sync Commands** (`Command\Sync\Command`):
- Wykonywane **synchronicznie** w tym samym request
- Używane gdy rezultat jest potrzebny od razu
- Transport: `sync://`
- Przykład: `RegisterUser` - musi się zakończyć przed zwróceniem odpowiedzi

**Async Commands** (`Command\Async\Command`):
- Wykonywane **asynchronicznie** przez worker
- Używane dla długotrwałych operacji, integracji zewnętrznych
- Transport: RabbitMQ AMQP
- Przykład: `FetchTvShowGenres` - może być wykonane w tle

**Kiedy używać Async?**
- ✅ Długotrwałe operacje (> 2 sekundy)
- ✅ Integracje z zewnętrznymi API
- ✅ Operacje, które mogą się powtórzyć w razie błędu
- ✅ Operacje nie wymagające natychmiastowej odpowiedzi
- ❌ NIE dla operacji krytycznych (np. płatności wymagające potwierdzenia)

#### Wywoływanie Commands

```php
// W kontrolerze
final readonly class SomeController extends AbstractApiController
{
    public function __construct(
        private readonly SyncCommandBus $commandBus,
        // lub: private readonly AsyncCommandBus $commandBus,
    ) {}

    #[Route('/api/some-action', methods: ['POST'])]
    public function action(#[MapRequestPayload] SomeRequest $request): Response
    {
        $this->commandBus->dispatch($request->toCommand());

        return $this->noContent();
    }
}
```

### Queries - Operacje Odczytu

**Queries** służą do pobierania danych bez modyfikacji stanu.

#### Query Interface

```php
namespace Bingely\{Module}\Application\Query;

interface SomeQuery
{
    public function execute(string $param): SomeResult;
}
```

**Zasady:**
- ✅ Query to **interface** w warstwie Application
- ✅ Metoda: `execute()` z parametrami
- ✅ Nazwa: rzeczownik opisujący pytanie (np. `UserExistsByEmail`, `GetTvShowById`)
- ✅ Znajduje się w `Application/Query/`
- ✅ Może zwracać dane (bool, DTO, Entity, array)

#### Query Implementation

```php
namespace Bingely\{Module}\Infrastructure\Doctrine\Query;

use Bingely\{Module}\Application\Query\SomeQuery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

final class SomeQueryImplementation extends ServiceEntityRepository implements SomeQuery
{
    public function execute(string $param): SomeResult
    {
        return $this->createQueryBuilder('alias')
            ->where('alias.field = :param')
            ->setParameter('param', $param)
            ->getQuery()
            ->getResult();
    }
}
```

**Zasady:**
- ✅ Implementacja w warstwie Infrastructure
- ✅ Rozszerza `ServiceEntityRepository` dla Doctrine
- ✅ Implementuje Query interface
- ✅ Znajduje się w `Infrastructure/Doctrine/Query/`
- ✅ Używa QueryBuilder lub DQL
- ❌ NIE modyfikuje stanu (tylko SELECT)

#### Używanie Queries

```php
// W Command Handlerze
final readonly class SomeCommandHandler implements CommandHandler
{
    public function __construct(
        private UserExistsByEmail $userExistsQuery,
    ) {}

    public function __invoke(SomeCommand $command): void
    {
        if ($this->userExistsQuery->execute($command->email)) {
            throw EmailAlreadyExistsException::withEmail($command->email);
        }

        // ... reszta logiki
    }
}
```

### CQS - Podsumowanie Zasad

| Aspekt | Command | Query |
|--------|---------|-------|
| **Cel** | Zmiana stanu | Odczyt danych |
| **Zwraca** | `void` | Dane (bool, DTO, Entity) |
| **Typ** | Readonly DTO | Interface |
| **Implementacja** | Handler z `__invoke()` | Klasa implementująca interface |
| **Lokalizacja** | `Application/Command/` | Interface: `Application/Query/`<br>Impl: `Infrastructure/Doctrine/Query/` |
| **Wywoływanie** | Przez Command Bus | Przez dependency injection |
| **Side effects** | TAK (zapis do DB, eventy) | NIE |

---

## Zasady i Konwencje

### 1. Encje Doctrine

**Używamy encji Doctrine bezpośrednio w warstwie Domain.**

```php
namespace Bingely\{Module}\Domain\Entity;

use Bingely\Shared\Domain\Entity\BaseEntity;
use Bingely\Shared\Domain\Trait\CreatedAtTrait;
use Bingely\Shared\Domain\Trait\UpdatedAtTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: Repository::class)]
#[ORM\Table(name: 'table_name')]
class SomeEntity extends BaseEntity
{
    use CreatedAtTrait;
    use UpdatedAtTrait;

    #[ORM\Column(type: 'string', unique: true)]
    private string $someField;

    // Gettery i settery
}
```

**Konwencje:**
- ✅ Dziedziczenie po `BaseEntity` (UUID v7 jako ID)
- ✅ Używanie trait: `CreatedAtTrait`, `UpdatedAtTrait`
- ✅ Atrybuty Doctrine bezpośrednio w encji
- ✅ Prywatne properties z getterami/setterami
- ✅ Immutable datetime: `DateTimeImmutable`
- ✅ Type hints wszędzie (strict types)
- ✅ Relacje definiowane atrybutami ORM
- ❌ NIE używamy publicznych properties (wyjątek: readonly DTOs)

### 2. Repozytoria

**Interfejs w Domain, implementacja w Infrastructure.**

#### Interface Repozytorium

```php
namespace Bingely\{Module}\Domain\Repository;

use Bingely\{Module}\Domain\Entity\SomeEntity;

interface SomeEntityRepository
{
    public function save(SomeEntity $entity): void;

    public function get(string $id): ?SomeEntity;

    // Opcjonalnie inne metody zapisu/usuwania
    public function delete(SomeEntity $entity): void;

    /** @param array<SomeEntity> $entities */
    public function saveMany(array $entities): void;
}
```

**Zasady:**
- ✅ Tylko metody **zapisu/usuwania** (Commands)
- ✅ Metody zwracają `void` albo encję
- ✅ Type hints dla parametrów i zwracanych wartości
- ❌ NIE umieszczamy queries w Repository (używamy osobnych Query interfaces)

#### Implementacja Repozytorium

```php
namespace Bingely\{Module}\Infrastructure\Doctrine\Repository\{Entity};

use Bingely\{Module}\Domain\Entity\SomeEntity;
use Bingely\{Module}\Domain\Repository\SomeEntityRepository as SomeEntityRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class Repository extends ServiceEntityRepository implements SomeEntityRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SomeEntity::class);
    }

    public function save(SomeEntity $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    public function get(string $id): ?SomeEntity
    {
        return $this->find($id);
    }
}
```

**Zasady:**
- ✅ Rozszerza `ServiceEntityRepository`
- ✅ Implementuje interfejs z Domain
- ✅ Nazwa: `Repository` (w katalogu `Infrastructure/Doctrine/Repository/{Entity}/`)
- ✅ Wywołuje `flush()` w metodach zapisu
- ✅ Metody `get*` mogą zwracać `null` jeśli nie znaleziono

### 3. Wyjątki Domenowe

**Hierarchy wyjątków:**

```
Exception (PHP)
└── DomainException (Shared)
    ├── ConflictDomainException (Shared)
    │   ├── EmailAlreadyExistsException (User)
    │   └── UsernameAlreadyExistsException (User)
    └── {Other}DomainException (Module)
```

#### Tworzenie Wyjątku Domenowego

```php
namespace Bingely\{Module}\Domain\Exception;

use Bingely\Shared\Domain\Exception\ConflictDomainException;

final class SomeConflictException extends ConflictDomainException
{
    public static function withSomeValue(string $value): self
    {
        return new self(
            sprintf('Some conflict occurred with value: %s', $value)
        );
    }
}
```

**Zasady:**
- ✅ Dziedziczenie po `DomainException` lub jego podklasach
- ✅ Metody statyczne `with*()` do tworzenia wyjątków
- ✅ Komunikaty czytelne dla dewelopera
- ✅ `final class` - nie dziedziczymy dalej
- ✅ Znajduje się w `Domain/Exception/`
- ❌ NIE używamy wyjątków do control flow

**Mapowanie na HTTP:**
- `ConflictDomainException` → 409 Conflict
- Inne `DomainException` → 400 Bad Request (domyślnie)

### 4. Eventy Domenowe

```php
namespace Bingely\{Module}\Domain\Event;

final readonly class SomethingHappened
{
    public function __construct(
        public string $entityId,
        public string $someData,
    ) {}
}
```

**Użycie w Handlerze:**

```php
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final readonly class SomeCommandHandler implements CommandHandler
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
    ) {}

    public function __invoke(SomeCommand $command): void
    {
        // ... logika

        $this->eventDispatcher->dispatch(
            new SomethingHappened($entity->getId(), $data)
        );
    }
}
```

**Zasady:**
- ✅ Event to **readonly DTO** z publicznymi propertiami
- ✅ Nazwa: **przeszły dokonany** (np. `UserRegistered`, nie `UserRegister`)
- ✅ Znajduje się w `Domain/Event/`
- ✅ Wysyłane przez `EventDispatcherInterface` w Handlerze
- ✅ Mogą być używane do komunikacji między modułami

### 5. Fabryki

**Fabryki enkapsulują złożoną logikę tworzenia encji.**

```php
namespace Bingely\{Module}\Application\Factory;

use Bingely\{Module}\Application\Command\Sync\SomeCommand;
use Bingely\{Module}\Domain\Entity\SomeEntity;

final readonly class SomeEntityFactory
{
    public function __construct(
        private SomeDependency $dependency,
    ) {}

    public function createFromCommand(SomeCommand $command): SomeEntity
    {
        $entity = new SomeEntity();
        $entity->setSomeField($this->dependency->process($command->field));

        return $entity;
    }
}
```

**Kiedy używać Factory?**
- ✅ Gdy tworzenie encji wymaga zależności (np. `PasswordHasher`)
- ✅ Gdy logika tworzenia jest złożona
- ✅ Gdy chcemy enkapsulować logikę tworzenia
- ❌ NIE dla prostych `new Entity()` - rób to bezpośrednio w Handlerze

### 6. Providers i Zewnętrzne Źródła Danych

**Provider to abstrakcja nad zewnętrznym źródłem danych (API, serwis).**

#### Interface Providera

```php
namespace Bingely\{Module}\Application\Provider;

use Bingely\{Module}\Application\Dto\SomeDto;

interface SomeProviderInterface
{
    public function getData(string $param): SomeDto;
}
```

**Zasady:**
- ✅ Interface w `Application/Provider/`
- ✅ Zwraca DTOs z warstwy Application
- ❌ **NIE powinien zwracać DTOs z Infrastructure** (to błąd!)

#### Implementacja Providera

```php
namespace Bingely\{Module}\Infrastructure\{ExternalService}\Provider;

use Bingely\{Module}\Application\Provider\SomeProviderInterface;
use Bingely\{Module}\Application\Dto\SomeDto;

final readonly class SomeProvider implements SomeProviderInterface
{
    public function __construct(
        private SomeClient $client,
        private SomeTransformer $transformer,
    ) {}

    public function getData(string $param): SomeDto
    {
        $response = $this->client->fetchData($param);

        return $this->transformer->transform($response);
    }
}
```

**Zasady:**
- ✅ Implementacja w `Infrastructure/{ServiceName}/Provider/`
- ✅ Używa klientów HTTP/zewnętrznych
- ✅ Transformuje odpowiedzi do DTOs Application
- ✅ Obsługuje błędy (try-catch, rzuca wyjątki domenowe)

### 7. DTOs (Data Transfer Objects)

**DTOs służą do przenoszenia danych między warstwami.**

```php
namespace Bingely\{Module}\Application\Dto;

final readonly class SomeDto
{
    public function __construct(
        public int $id,
        public string $name,
        public string $description,
    ) {}
}
```

**Zasady:**
- ✅ **Readonly** klasy z publicznymi propertiami
- ✅ Immutable (wartości ustawiane tylko w konstruktorze)
- ✅ Type hints na wszystkich propertiach
- ✅ Znajdują się w `Application/Dto/` lub `UserInterface/Request/`
- ❌ NIE zawierają logiki biznesowej
- ❌ NIE mają setterów

**DTOs w różnych warstwach:**
- **Application/Dto/** - DTOs używane w warstwie aplikacyjnej
- **UserInterface/Request/** - DTOs z walidacją dla HTTP requests
- **Infrastructure/{Service}/Dto/** - DTOs specyficzne dla zewnętrznej integracji (powinny być transformowane do Application DTOs!)

### 8. Kontrolery

**Kontrolery powinny być cienkie - tylko routing i delegacja.**

```php
namespace Bingely\{Module}\UserInterface\Controller;

use Bingely\Shared\Infrastructure\Symfony\Controller\AbstractApiController;
use Bingely\Shared\Application\Command\Sync\SyncCommandBus;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

final readonly class SomeController extends AbstractApiController
{
    public function __construct(
        private readonly SyncCommandBus $commandBus,
    ) {}

    #[Route('/api/some-action', methods: ['POST'])]
    public function action(#[MapRequestPayload] SomeRequest $request): Response
    {
        $this->commandBus->dispatch($request->toCommand());

        return $this->noContent(); // 204
        // lub: return $this->success($data); // 200
        // lub: return $this->created($data); // 201
    }
}
```

**Zasady:**
- ✅ Dziedziczy po `AbstractApiController`
- ✅ Readonly klasa z dependency injection
- ✅ Używa `#[MapRequestPayload]` do walidacji
- ✅ Deleguje do Command Bus
- ✅ Zwraca odpowiedź przez helpery (`success()`, `noContent()`, `created()`)
- ❌ NIE zawiera logiki biznesowej
- ❌ NIE wywołuje serwisów bezpośrednio (tylko przez Command Bus)

### 9. Request DTOs z Walidacją

```php
namespace Bingely\{Module}\UserInterface\Request;

use Symfony\Component\Validator\Constraints as Assert;
use Bingely\{Module}\Application\Command\Sync\SomeCommand;

final readonly class SomeRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,

        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 50)]
        public string $name,
    ) {}

    public function toCommand(): SomeCommand
    {
        return new SomeCommand(
            email: $this->email,
            name: $this->name,
        );
    }
}
```

**Zasady:**
- ✅ Readonly klasa
- ✅ Walidacja Symfony Validator (atrybuty `Assert`)
- ✅ Metoda `toCommand()` konwertująca do Command
- ✅ Znajduje się w `UserInterface/Request/`
- ❌ NIE zawiera logiki biznesowej (tylko walidacja formatów)

### 10. Enumy

**Używamy PHP 8.1 enums dla wartości wyliczeniowych.**

```php
namespace Bingely\{Module}\Domain\Enum;

enum SomeEnum: string
{
    case OPTION_ONE = 'option_one';
    case OPTION_TWO = 'option_two';

    public function getLabel(): string
    {
        return match ($this) {
            self::OPTION_ONE => 'Option One',
            self::OPTION_TWO => 'Option Two',
        };
    }
}
```

**Zasady:**
- ✅ Backed enum (string lub int)
- ✅ Znajduje się w `Domain/Enum/`
- ✅ Może mieć metody pomocnicze
- ✅ Używany w encjach jako typ kolumny

---

## Wzorce Projektowe

### Stosowane Wzorce

1. **Command Pattern** - Commands i Handlers
2. **Query Object Pattern** - Query interfaces
3. **Repository Pattern** - Abstrakcja nad persystencją
4. **Factory Pattern** - Tworzenie złożonych obiektów
5. **Provider Pattern** - Abstrakcja nad zewnętrznymi źródłami
6. **Transformer Pattern** - Transformacja danych między formatami
7. **Event Dispatcher Pattern** - Eventy domenowe
8. **DTO Pattern** - Przenoszenie danych
9. **Strategy Pattern** - Filtry (FilterInterface)
10. **Dependency Injection** - Autowiring przez konstruktor

### Wzorce które NIE Stosujemy

❌ **Aggregate Root** - Zbyt złożony wzorzec dla naszych potrzeb
❌ **Value Objects** - Używamy skalarów tam gdzie wystarczą
❌ **Domain Services** - Logika w Handlerach lub Factories
❌ **Specification Pattern** - Używamy Doctrine QueryBuilder
❌ **Data Mapper** - Używamy Doctrine ORM bezpośrednio

---

## Problemy do Naprawy

### 🔴 Krytyczne - Do natychmiastowej naprawy

#### 1. Naruszenie Dependency Rule w TvShowProviderInterface

**Problem:**
```php
// src/TvShow/Application/Provider/TvShowProviderInterface.php
namespace Bingely\TvShow\Application\Provider;

use Bingely\TvShow\Infrastructure\Tmdb\Dto\TvShowCollectionDto; // ❌ BŁĄD!
use Bingely\TvShow\Infrastructure\Tmdb\Filter\FilterInterface; // ❌ BŁĄD!

interface TvShowProviderInterface
{
    public function getPopular(
        Language $language = Language::ENGLISH,
        int $page = 1,
        array $filters = [], // FilterInterface z Infrastructure
    ): TvShowCollectionDto; // DTO z Infrastructure
}
```

**Dlaczego to błąd:**
- Warstwa Application **NIE MOŻE** zależeć od Infrastructure
- To naruszenie zasad Clean Architecture
- Utrudnia testowanie i zmianę implementacji

**Rozwiązanie:**
1. Przenieś `TvShowCollectionDto` i `TvShowDto` do `Application/Dto/TvShow/`
2. Przenieś `FilterInterface` do `Application/Filter/`
3. W Infrastructure utwórz Transformer który mapuje z DTOs TMDB do DTOs Application

#### 2. Niespójność w Pattern Repository vs Query

**Problem:**
- Moduł `User`: Query interfaces osobno (`UserExistsByEmail`, `UserExistsByUsername`)
- Moduł `TvShow`: Metody query w Repository (`getByTmdbId()`, `getAll()`)

**Rozwiązanie:**
Standaryzuj na podejście z modułu User (Query interfaces):

```php
// TvShowGenreRepository powinno mieć tylko save/delete
interface TvShowGenreRepository
{
    public function save(TvShowGenre $genre): void;
    public function saveMany(array $genres): void;
    public function delete(TvShowGenre $genre): void;
}

// Nowe Query interfaces
interface GetTvShowGenreById
{
    public function execute(string $id): ?TvShowGenre;
}

interface GetTvShowGenreByTmdbId
{
    public function execute(int $tmdbId): ?TvShowGenre;
}

interface GetAllTvShowGenres
{
    /** @return array<TvShowGenre> */
    public function execute(): array;
}
```

### 🟡 Ważne - Do naprawy w najbliższym czasie

#### 3. Brak Konsekwencji w Naming Repository Interfaces

**Problem:**
- `UserRepositoryInterface` - z suffiksem "Interface"
- `TvShowGenreRepository` - bez suffiksu "Interface"

**Rozwiązanie:**
Zdecyduj się na jeden standard:
- **Opcja A (preferowana):** Bez suffiksu - `UserRepository`, `TvShowGenreRepository`
- **Opcja B:** Z suffiksem - `UserRepositoryInterface`, `TvShowGenreRepositoryInterface`

Zalecam **Opcję A** (bez suffiksu) jako bardziej czytelną.

#### 4. Brak TvShow Repository

**Problem:**
Mamy encję `TvShow` ale brak `TvShowRepository` interface i implementacji.

**Rozwiązanie:**
Dodaj Repository dla TvShow jeśli encje są zapisywane do bazy (obecnie wygląda że są tylko fetchowane z API).

### 🟢 Nice to have - Ulepszenia do rozważenia

#### 5. Duplikacja Logiki w Infrastructure Repository

**Obserwacja:**
Każda implementacja Repository ma takie same metody `save()`, `saveMany()` etc.

**Możliwe rozwiązanie:**
Stwórz `AbstractDoctrineRepository` w Shared:

```php
namespace Bingely\Shared\Infrastructure\Doctrine\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

abstract class AbstractDoctrineRepository extends ServiceEntityRepository
{
    public function save(object $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    public function saveMany(array $entities): void
    {
        foreach ($entities as $entity) {
            $this->getEntityManager()->persist($entity);
        }
        $this->getEntityManager()->flush();
    }

    public function delete(object $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }
}
```

---

## Potencjalne Ulepszenia

### 1. Automatyczna Walidacja Architektury

**Narzędzie:** deptrac (https://github.com/qossmic/deptrac)

Możesz dodać automatyczną walidację zależności między warstwami:

```yaml
# deptrac.yaml
paths:
  - ./src

layers:
  - name: Domain
    collectors:
      - type: directory
        regex: src/.*/Domain/.*
  - name: Application
    collectors:
      - type: directory
        regex: src/.*/Application/.*
  - name: Infrastructure
    collectors:
      - type: directory
        regex: src/.*/Infrastructure/.*
  - name: UserInterface
    collectors:
      - type: directory
        regex: src/.*/UserInterface/.*

ruleset:
  Domain: ~
  Application:
    - Domain
  Infrastructure:
    - Domain
    - Application
  UserInterface:
    - Application
    - Domain
```

### 2. Dokumentacja Modułów

Dodaj `README.md` do każdego modułu opisujący:
- Odpowiedzialność modułu
- Główne use cases
- API endpoints (jeśli dotyczy)
- Eventy publikowane
- Zależności zewnętrzne

### 3. Testy Architektoniczne

**Narzędzie:** PHPArch (https://github.com/j6s/phparch)

Automatyczne testy sprawdzające:
- Czy Application nie zależy od Infrastructure ✅
- Czy Domain nie zależy od niczego ✅
- Czy moduły nie mają cross-dependencies ✅

### 4. OpenAPI Documentation

Rozważ dodanie automatycznej dokumentacji API przez:
- **NelmioApiDocBundle** lub
- **API Platform** (jeśli API będzie bardziej rozbudowane)

### 5. Read Models dla Złożonych Queries

Jeśli queries staną się bardziej złożone, rozważ:
- Osobne read models (CQRS w pełni)
- Denormalizowane widoki w bazie
- ElasticSearch dla zaawansowanego wyszukiwania

### 6. Async Event Handlers

Obecnie eventy są synchroniczne. Możesz:
- Dodać `AsyncEventBus` podobnie jak `AsyncCommandBus`
- Wysyłać niektóre eventy do kolejki
- Obsługiwać side-effects asynchronicznie

### 7. Standardowy Format API Responses

Rozważ standardowy format wszystkich odpowiedzi:

```json
{
  "data": { ... },
  "meta": {
    "timestamp": "2025-01-13T10:00:00Z",
    "requestId": "uuid"
  }
}
```

lub format JSON:API (https://jsonapi.org/)

### 8. Rate Limiting dla Zewnętrznych API

Dodaj rate limiting dla wywołań do TMDB API:
- Symfony Rate Limiter component
- Cache responses
- Circuit breaker pattern

---

## Podsumowanie

### ✅ Co działa dobrze

1. **Modułowa struktura** - Moduły User i TvShow są dobrze oddzielone
2. **CQS Pattern** - Command Bus i Query interfaces działają poprawnie
3. **Pragmatyczne podejście** - Encje Doctrine bezpośrednio używane bez nadmiernej abstrakcji
4. **Dependency Injection** - Autowiring działa świetnie
5. **Eventy domenowe** - Umożliwiają komunikację między modułami
6. **Testy** - Pokrycie testami jednostkowymi i funkcjonalnymi

### 🔧 Co wymaga naprawy

1. **Dependency Rule Violation** - Application zależy od Infrastructure w `TvShowProviderInterface`
2. **Niespójność Repository/Query** - Różne podejścia w User vs TvShow
3. **Naming conventions** - Repository interfaces bez spójnego nazwnictwa

### 🚀 Co można ulepszyć

1. Automatyczna walidacja architektury (deptrac)
2. Dokumentacja modułów
3. Testy architektoniczne
4. Read models dla złożonych queries
5. Async event handlers

---

## Checklist dla Nowych Feature'ów

Przy dodawaniu nowej funkcjonalności sprawdź:

- [ ] Czy feature należy do istniejącego modułu czy potrzebny nowy moduł?
- [ ] Czy utworzono Command dla operacji zapisu?
- [ ] Czy utworzono Query interface dla operacji odczytu?
- [ ] Czy Command Handler wysyła event domenowy jeśli potrzeba?
- [ ] Czy Repository interface ma tylko metody zapisu/usuwania?
- [ ] Czy Query implementation jest w Infrastructure?
- [ ] Czy nie ma naruszenia Dependency Rule (Application → Infrastructure)?
- [ ] Czy testy pokrywają happy path i edge cases?
- [ ] Czy DTOs są readonly?
- [ ] Czy encje mają CreatedAt i UpdatedAt traits?
- [ ] Czy wyjątki domenowe dziedziczą po DomainException?
- [ ] Czy kontroler jest cienki (tylko delegacja)?

---

**Wersja:** 1.0
**Data:** 2025-01-13
**Autor:** Architektura Bingely Team

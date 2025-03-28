[![codecov](https://codecov.io/gh/vladitot/arch-inspector/branch/master/graph/badge.svg?token=5QHKLNTFLC)](https://codecov.io/gh/vladitot/arch-inspector)

# 🏛 Arch Inspector (Archi)

**Arch Inspector**, or simply **Archi**, is a PHP architecture rules checker.  
It helps you describe architectural rules for your project and enforce them across the team.

---

## 🔧 Features

- Define architectural rules in plain PHP.
- Automatically check your codebase against those rules.
- Easily integrate into your CI/CD pipeline.
- Simple to use and highly extensible.

---

## 🚀 Quick Start

### 1. Install Archi

Globally:

```bash
composer global require vladitot/arch-inspector
```

Or locally in your project:

```bash
composer require --dev vladitot/arch-inspector
```

### 2. Create the configuration file

Create a file named `archi.php` in the root of your project:

```php
<?php

return [
    \Vladitot\ArchChecker\Rules\RuleForSomeNamespace::filter([
        new \Vladitot\ArchChecker\Filters\Each(),
    ])
        ->should([
            new \Vladitot\ArchChecker\Should\NotExist(),
        ])
        ->setRuleName('My Personal Rule Name'),
];
```

### 3. Run the check

```bash
./vendor/bin/archi c
```

### 4. Add to CI/CD (optional)

Make sure your code adheres to architectural rules automatically in your pipelines.

---

## 📦 CLI Commands

```bash
./vendor/bin/archi c
```

Runs architecture checks based on your defined rules.

```bash
./vendor/bin/archi b
```

Generates a new baseline of current violations.

---

## 🧱 Rule Structure

Each rule consists of:

- A **target** (`RuleForSome*`)
- A set of **filters**
- One or more **expectations** (called "shoulds")

### 📁 Available Rules

- `RuleForSomeClass`
- `RuleForSomeInterface`
- `RuleForSomeMethod`
- `RuleForSomeNamespace`
- `RuleForSomeTrait`

### 🧲 Available Filters

- `Each`
- `UnderNamespace`
- `WhichExtends`
- `WhichHasAttribute`
- `WhichImplements`
- `WhichIsAbstract`
- `WhichIsFinal`
- `WithName`

### ✅ Available Shoulds

- `NotBeHigherNLines`
- `NotExist`
- `NotHaveStaticMethods`
- `NotHaveStaticVariables`
- `NotToBeInANamespace`
- `NotUseAnyClassExtendsSpecified`

---

## 📄 Example Configuration

```php
<?php

use Vladitot\ArchChecker\Rules\RuleForSomeNamespace;
use Vladitot\ArchChecker\Filters\Each;
use Vladitot\ArchChecker\Should\NotExist;

return [
    RuleForSomeNamespace::filter([
        new Each(),
    ])
        ->should([
            new NotExist(),
        ])
        ->setRuleName('Disallow this namespace completely'),
];
```

---

## 💡 Why Use Archi?

- Enforces architectural rules across your codebase.
- Acts as living documentation for your architecture.
- Prevents architectural drift and technical debt.
- Keeps code maintainable, scalable, and consistent.

---

## 🤝 Contributing

We welcome issues, pull requests, and ideas!  
Let’s build a more structured PHP world together.

---

## 🪪 License

This project is licensed under the MIT License.
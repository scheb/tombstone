<?php

/** @psalm-suppress all */
// phpcs:ignoreFile

namespace {
    use Tombstone as Tombstone2;
    use function Tombstone\Func\ns_tombstone;
    use function Tombstone\Func\ns_tombstone as ns_tombstone2;

    // Global function
    tombstone();  // Ok
    \tombstone(); // Ok

    // Namespaced function
    ns_tombstone();   // Ok
    \ns_tombstone();  // Ignore

    // Namespaced function - alias
    ns_tombstone2();  // Ok
    \ns_tombstone2(); // Ignore

    // Namespaced function - FQN
    Tombstone\Func\ns_tombstone();  // Ok
    \Tombstone\Func\ns_tombstone(); // Ok

    // Namespaced function - FQN with namespace alias
    Tombstone2\Func\ns_tombstone();  // Ok
    \Tombstone2\Func\ns_tombstone(); // Ignore
}

namespace Foo {
    use Tombstone as Tombstone2;
    use function Tombstone\Func\ns_tombstone;
    use function Tombstone\Func\ns_tombstone as ns_tombstone2;

    // Global function
    tombstone();  // Ok
    \tombstone(); // Ok

    // Namespaced function
    ns_tombstone();   // Ok
    \ns_tombstone();  // Ignore

    // Namespaced function - alias
    ns_tombstone2();  // Ok
    \ns_tombstone2(); // Ignore

    // Namespaced function - FQN
    Tombstone\Func\ns_tombstone();  // Ignore
    \Tombstone\Func\ns_tombstone(); // Ok

    // Namespaced function - FQN with namespace alias
    Tombstone2\Func\ns_tombstone();  // Ok
    \Tombstone2\Func\ns_tombstone(); // Ignore
}

namespace Tombstone\Func {
    use Tombstone as Tombstone2;
    use function Tombstone\Func\ns_tombstone as ns_tombstone2;

    // Global function
    tombstone();  // Ok
    \tombstone(); // Ok

    // Namespaced function
    ns_tombstone();   // Ok
    \ns_tombstone();  // Ignore

    // Namespaced function - alias
    ns_tombstone2();  // Ok
    \ns_tombstone2(); // Ignore

    // Namespaced function - FQN
    Tombstone\Func\ns_tombstone();  // Ignore
    \Tombstone\Func\ns_tombstone(); // Ok

    // Namespaced function - FQN with namespace alias
    Tombstone2\Func\ns_tombstone();  // Ok
    \Tombstone2\Func\ns_tombstone(); // Ignore

    function ns_tombstone(): void
    {
    }
}

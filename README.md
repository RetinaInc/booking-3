# Booking component

### Introduction

Booking component is a framework agnostic module that implements basic functionality of booking rooms.

### Requirements

* PHP >= 5.4.0 (Tested with 5.5.9)

### Installation

The project requires Composer since it is used to load classes automatically.

```sh
composer install
```

### Usage

In order to use this module you need to implement the repository interfaces that handle data. 
After that Vmiki\Booking\BookingHandler can be used to book rooms.

```php
$bookingHandler = new BookingHandler(
    $myBookingRepository, $myTenantRepository, $myHouseRepository, $myRoomRepository
);
$bookingHandler->bookRoom(
    $tenantId, $roomId, new DateTime('2014-12-20'), new DateTime('2015-02-20')
);
```
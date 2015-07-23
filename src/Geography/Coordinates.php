<?php

namespace Common\ValueObject\Geography;

use InvalidArgumentException;

/**
 * Represents a geographic point location.
 *
 * @author Marcos Passos <marcos@marcospassos.com>
 *
 * @see    https://en.wikipedia.org/wiki/Geographic_coordinate_system
 */
class Coordinates
{
    /**
     * The latitude in decimal degrees.
     *
     * @var float
     */
    private $latitude;

    /**
     * The longitude in decimal degrees.
     *
     * @var float
     */
    private $longitude;

    /**
     * The altitude in meters.
     *
     * @var float|null
     */
    private $altitude;

    /**
     * Constructor.
     *
     * @param float      $latitude  The latitude in decimal degrees, must range from -90 to 90.
     * @param float      $longitude The longitude in decimal degrees, must range from -180 to 180.
     * @param float|null $altitude  The altitude in meters.
     *
     * @throws InvalidArgumentException When the latitude is invalid.
     * @throws InvalidArgumentException When the longitude is invalid.
     */
    public function __construct($latitude, $longitude, $altitude = null)
    {
        if (!is_numeric($latitude) || $latitude < -90 || $latitude > 90) {
            throw new InvalidArgumentException(sprintf(
                'The latitude must be a number ranging from -90 to 90, %s given.',
                $latitude
            ));
        }

        if (!is_numeric($longitude) ||  $longitude < -180 || $longitude > 180) {
            throw new InvalidArgumentException(sprintf(
                'The longitude must be a number ranging from -180 to 180, %s given.',
                $longitude
            ));
        }

        $this->latitude = (float) $latitude;
        $this->longitude = (float) $longitude;
        $this->altitude = null !== $altitude ? (float) $altitude: null;
    }

    /**
     * Parses an ISO 6709 Standard representation of geographic point location
     * by coordinates in into an object.
     *
     * @param string $coordinates The geographic point.
     *
     * @return Coordinates
     *
     * @throws InvalidArgumentException If the coordinates string is malformed.
     *
     * @see https://en.wikipedia.org/wiki/ISO_6709
     */
    public function fromString($coordinates)
    {
        if (!preg_match('/[0-9]+\.[0-9]+ [0-9]+\.[0-9]+( [0-9]+\.[0-9]+/)?', $coordinates)) {
            throw new InvalidArgumentException('Malformed coordinates string.');
        }

        $components = explode(" ", $coordinates);

        return new self($components[0], $components[1], isset($components[2]) ? $components[2] : null);
    }

    /**
     * Returns the latitude in decimal degrees.
     *
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Returns the longitude in decimal degrees.
     *
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Returns the altitude in meters, if available.
     *
     * @return float|null
     */
    public function getAltitude()
    {
        return $this->altitude;
    }

    /**
     * Checks whether the altitude coordinate is available.
     *
     * @return boolean `true` if the altitude coordinate is available, `false` otherwise.
     */
    public function hasAltitude()
    {
        return null !== $this->altitude;
    }

    /**
     * Returns the ISO 6709 Standard representation of geographic point location
     * by coordinates.
     *
     * @return string
     *
     * @see https://en.wikipedia.org/wiki/ISO_6709
     */
    public function __toString()
    {
        if (!$this->hasAltitude()) {
            return sprintf('%f %f', $this->latitude, $this->longitude);
        }

        return sprintf('%f %f %f', $this->latitude, $this->longitude, $this->altitude);
    }
}
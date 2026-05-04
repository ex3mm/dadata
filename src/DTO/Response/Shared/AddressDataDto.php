<?php

declare(strict_types=1);

namespace Ex3mm\Dadata\DTO\Response\Shared;

use Ex3mm\Dadata\DTO\Response\Shared\Address\DivisionsDto;
use Ex3mm\Dadata\DTO\Response\Shared\Address\MetroStationDto;

/**
 * Детальные данные адреса из DaData API.
 *
 * @codeCoverageIgnore Конструктор с 120+ параметрами покрывается через fromArray()
 */
final readonly class AddressDataDto
{
    /**
     * @param string|null $postalCode Почтовый индекс
     * @param string|null $country Страна
     * @param string|null $countryIsoCode ISO код страны
     * @param string|null $federalDistrict Федеральный округ
     * @param string|null $regionFiasId ФИАС ID региона
     * @param string|null $regionKladrId КЛАДР ID региона
     * @param string|null $regionIsoCode ISO код региона
     * @param string|null $regionWithType Регион с типом
     * @param string|null $regionType Тип региона (сокращенный)
     * @param string|null $regionTypeFull Тип региона (полный)
     * @param string|null $region Регион
     * @param string|null $areaFiasId ФИАС ID района
     * @param string|null $areaKladrId КЛАДР ID района
     * @param string|null $areaWithType Район с типом
     * @param string|null $areaType Тип района (сокращенный)
     * @param string|null $areaTypeFull Тип района (полный)
     * @param string|null $area Район
     * @param string|null $subAreaFiasId ФИАС ID мун. поселения
     * @param string|null $subAreaKladrId КЛАДР ID мун. поселения
     * @param string|null $subAreaWithType Мун. поселение с типом
     * @param string|null $subAreaType Тип мун. поселения (сокращенный)
     * @param string|null $subAreaTypeFull Тип мун. поселения (полный)
     * @param string|null $subArea Мун. поселение
     * @param string|null $cityFiasId ФИАС ID города
     * @param string|null $cityKladrId КЛАДР ID города
     * @param string|null $cityWithType Город с типом
     * @param string|null $cityType Тип города (сокращенный)
     * @param string|null $cityTypeFull Тип города (полный)
     * @param string|null $city Город
     * @param string|null $cityArea Административный округ города
     * @param string|null $cityDistrictFiasId ФИАС ID района города
     * @param string|null $cityDistrictKladrId КЛАДР ID района города
     * @param string|null $cityDistrictWithType Район города с типом
     * @param string|null $cityDistrictType Тип района города (сокращенный)
     * @param string|null $cityDistrictTypeFull Тип района города (полный)
     * @param string|null $cityDistrict Район города
     * @param string|null $settlementFiasId ФИАС ID населенного пункта
     * @param string|null $settlementKladrId КЛАДР ID населенного пункта
     * @param string|null $settlementWithType Населенный пункт с типом
     * @param string|null $settlementType Тип населенного пункта (сокращенный)
     * @param string|null $settlementTypeFull Тип населенного пункта (полный)
     * @param string|null $settlement Населенный пункт
     * @param string|null $streetFiasId ФИАС ID улицы
     * @param string|null $streetKladrId КЛАДР ID улицы
     * @param string|null $streetWithType Улица с типом
     * @param string|null $streetType Тип улицы (сокращенный)
     * @param string|null $streetTypeFull Тип улицы (полный)
     * @param string|null $street Улица
     * @param string|null $steadFiasId ФИАС ID земельного участка
     * @param string|null $steadKladrId КЛАДР ID земельного участка
     * @param string|null $steadCadnum Кадастровый номер участка
     * @param string|null $steadType Тип участка (сокращенный)
     * @param string|null $steadTypeFull Тип участка (полный)
     * @param string|null $stead Участок
     * @param string|null $houseFiasId ФИАС ID дома
     * @param string|null $houseKladrId КЛАДР ID дома
     * @param string|null $houseCadnum Кадастровый номер дома
     * @param int|null $houseFlatCount Количество квартир в доме
     * @param string|null $houseType Тип дома (сокращенный)
     * @param string|null $houseTypeFull Тип дома (полный)
     * @param string|null $house Дом
     * @param string|null $blockType Тип корпуса (сокращенный)
     * @param string|null $blockTypeFull Тип корпуса (полный)
     * @param string|null $block Корпус
     * @param string|null $entrance Подъезд
     * @param string|null $floor Этаж
     * @param string|null $flatFiasId ФИАС ID квартиры
     * @param string|null $flatCadnum Кадастровый номер квартиры
     * @param string|null $flatType Тип квартиры (сокращенный)
     * @param string|null $flatTypeFull Тип квартиры (полный)
     * @param string|null $flat Квартира
     * @param float|null $flatArea Площадь квартиры
     * @param float|null $squareMeterPrice Цена за квадратный метр
     * @param float|null $flatPrice Стоимость квартиры
     * @param string|null $roomFiasId ФИАС ID комнаты
     * @param string|null $roomCadnum Кадастровый номер комнаты
     * @param string|null $roomType Тип комнаты (сокращенный)
     * @param string|null $roomTypeFull Тип комнаты (полный)
     * @param string|null $room Комната
     * @param string|null $postalBox Абонентский ящик
     * @param string|null $fiasId ФИАС ID
     * @param string|null $fiasCode Код ФИАС
     * @param string|null $fiasLevel Уровень детализации ФИАС
     * @param string|null $fiasActualityState Признак актуальности ФИАС
     * @param string|null $kladrId КЛАДР ID
     * @param string|null $geonameId GeoNames ID
     * @param string|null $capitalMarker Признак центра района/региона
     * @param string|null $okato ОКАТО
     * @param string|null $oktmo ОКТМО
     * @param string|null $taxOffice Код ИФНС для физических лиц
     * @param string|null $taxOfficeLegal Код ИФНС для организаций
     * @param string|null $timezone Часовой пояс
     * @param float|null $geoLat Широта
     * @param float|null $geoLon Долгота
     * @param string|null $beltwayHit Внутри кольцевой дороги
     * @param int|null $beltwayDistance Расстояние до кольцевой дороги в км
     * @param list<MetroStationDto>|null $metro Список ближайших станций метро
     * @param DivisionsDto|null $divisions Данные о подразделении ФМС
     * @param string|null $qcGeo Код качества координат
     * @param string|null $qcComplete Код полноты адреса
     * @param string|null $qcHouse Код проверки дома
     * @param list<string>|null $historyValues Исторические названия
     * @param string|null $unparsedParts Нераспознанная часть адреса
     * @param string|null $source Исходная строка
     * @param string|null $qc Код качества
     */
    public function __construct(
        public ?string $postalCode,
        public ?string $country,
        public ?string $countryIsoCode,
        public ?string $federalDistrict,
        public ?string $regionFiasId,
        public ?string $regionKladrId,
        public ?string $regionIsoCode,
        public ?string $regionWithType,
        public ?string $regionType,
        public ?string $regionTypeFull,
        public ?string $region,
        public ?string $areaFiasId,
        public ?string $areaKladrId,
        public ?string $areaWithType,
        public ?string $areaType,
        public ?string $areaTypeFull,
        public ?string $area,
        public ?string $subAreaFiasId,
        public ?string $subAreaKladrId,
        public ?string $subAreaWithType,
        public ?string $subAreaType,
        public ?string $subAreaTypeFull,
        public ?string $subArea,
        public ?string $cityFiasId,
        public ?string $cityKladrId,
        public ?string $cityWithType,
        public ?string $cityType,
        public ?string $cityTypeFull,
        public ?string $city,
        public ?string $cityArea,
        public ?string $cityDistrictFiasId,
        public ?string $cityDistrictKladrId,
        public ?string $cityDistrictWithType,
        public ?string $cityDistrictType,
        public ?string $cityDistrictTypeFull,
        public ?string $cityDistrict,
        public ?string $settlementFiasId,
        public ?string $settlementKladrId,
        public ?string $settlementWithType,
        public ?string $settlementType,
        public ?string $settlementTypeFull,
        public ?string $settlement,
        public ?string $streetFiasId,
        public ?string $streetKladrId,
        public ?string $streetWithType,
        public ?string $streetType,
        public ?string $streetTypeFull,
        public ?string $street,
        public ?string $steadFiasId,
        public ?string $steadKladrId,
        public ?string $steadCadnum,
        public ?string $steadType,
        public ?string $steadTypeFull,
        public ?string $stead,
        public ?string $houseFiasId,
        public ?string $houseKladrId,
        public ?string $houseCadnum,
        public ?int $houseFlatCount,
        public ?string $houseType,
        public ?string $houseTypeFull,
        public ?string $house,
        public ?string $blockType,
        public ?string $blockTypeFull,
        public ?string $block,
        public ?string $entrance,
        public ?string $floor,
        public ?string $flatFiasId,
        public ?string $flatCadnum,
        public ?string $flatType,
        public ?string $flatTypeFull,
        public ?string $flat,
        public ?float $flatArea,
        public ?float $squareMeterPrice,
        public ?float $flatPrice,
        public ?string $roomFiasId,
        public ?string $roomCadnum,
        public ?string $roomType,
        public ?string $roomTypeFull,
        public ?string $room,
        public ?string $postalBox,
        public ?string $fiasId,
        public ?string $fiasCode,
        public ?string $fiasLevel,
        public ?string $fiasActualityState,
        public ?string $kladrId,
        public ?string $geonameId,
        public ?string $capitalMarker,
        public ?string $okato,
        public ?string $oktmo,
        public ?string $taxOffice,
        public ?string $taxOfficeLegal,
        public ?string $timezone,
        public ?float $geoLat,
        public ?float $geoLon,
        public ?string $beltwayHit,
        public ?int $beltwayDistance,
        /** @var list<MetroStationDto>|null */
        public ?array $metro,
        public ?DivisionsDto $divisions,
        public ?string $qcGeo,
        public ?string $qcComplete,
        public ?string $qcHouse,
        /** @var list<string>|null */
        public ?array $historyValues,
        public ?string $unparsedParts,
        public ?string $source,
        public ?string $qc,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        return new self(
            postalCode: self::extractString($data, 'postal_code'),
            country: self::extractString($data, 'country'),
            countryIsoCode: self::extractString($data, 'country_iso_code'),
            federalDistrict: self::extractString($data, 'federal_district'),
            regionFiasId: self::extractString($data, 'region_fias_id'),
            regionKladrId: self::extractString($data, 'region_kladr_id'),
            regionIsoCode: self::extractString($data, 'region_iso_code'),
            regionWithType: self::extractString($data, 'region_with_type'),
            regionType: self::extractString($data, 'region_type'),
            regionTypeFull: self::extractString($data, 'region_type_full'),
            region: self::extractString($data, 'region'),
            areaFiasId: self::extractString($data, 'area_fias_id'),
            areaKladrId: self::extractString($data, 'area_kladr_id'),
            areaWithType: self::extractString($data, 'area_with_type'),
            areaType: self::extractString($data, 'area_type'),
            areaTypeFull: self::extractString($data, 'area_type_full'),
            area: self::extractString($data, 'area'),
            subAreaFiasId: self::extractString($data, 'sub_area_fias_id'),
            subAreaKladrId: self::extractString($data, 'sub_area_kladr_id'),
            subAreaWithType: self::extractString($data, 'sub_area_with_type'),
            subAreaType: self::extractString($data, 'sub_area_type'),
            subAreaTypeFull: self::extractString($data, 'sub_area_type_full'),
            subArea: self::extractString($data, 'sub_area'),
            cityFiasId: self::extractString($data, 'city_fias_id'),
            cityKladrId: self::extractString($data, 'city_kladr_id'),
            cityWithType: self::extractString($data, 'city_with_type'),
            cityType: self::extractString($data, 'city_type'),
            cityTypeFull: self::extractString($data, 'city_type_full'),
            city: self::extractString($data, 'city'),
            cityArea: self::extractString($data, 'city_area'),
            cityDistrictFiasId: self::extractString($data, 'city_district_fias_id'),
            cityDistrictKladrId: self::extractString($data, 'city_district_kladr_id'),
            cityDistrictWithType: self::extractString($data, 'city_district_with_type'),
            cityDistrictType: self::extractString($data, 'city_district_type'),
            cityDistrictTypeFull: self::extractString($data, 'city_district_type_full'),
            cityDistrict: self::extractString($data, 'city_district'),
            settlementFiasId: self::extractString($data, 'settlement_fias_id'),
            settlementKladrId: self::extractString($data, 'settlement_kladr_id'),
            settlementWithType: self::extractString($data, 'settlement_with_type'),
            settlementType: self::extractString($data, 'settlement_type'),
            settlementTypeFull: self::extractString($data, 'settlement_type_full'),
            settlement: self::extractString($data, 'settlement'),
            streetFiasId: self::extractString($data, 'street_fias_id'),
            streetKladrId: self::extractString($data, 'street_kladr_id'),
            streetWithType: self::extractString($data, 'street_with_type'),
            streetType: self::extractString($data, 'street_type'),
            streetTypeFull: self::extractString($data, 'street_type_full'),
            street: self::extractString($data, 'street'),
            steadFiasId: self::extractString($data, 'stead_fias_id'),
            steadKladrId: self::extractString($data, 'stead_kladr_id'),
            steadCadnum: self::extractString($data, 'stead_cadnum'),
            steadType: self::extractString($data, 'stead_type'),
            steadTypeFull: self::extractString($data, 'stead_type_full'),
            stead: self::extractString($data, 'stead'),
            houseFiasId: self::extractString($data, 'house_fias_id'),
            houseKladrId: self::extractString($data, 'house_kladr_id'),
            houseCadnum: self::extractString($data, 'house_cadnum'),
            houseFlatCount: self::extractInt($data, 'house_flat_count'),
            houseType: self::extractString($data, 'house_type'),
            houseTypeFull: self::extractString($data, 'house_type_full'),
            house: self::extractString($data, 'house'),
            blockType: self::extractString($data, 'block_type'),
            blockTypeFull: self::extractString($data, 'block_type_full'),
            block: self::extractString($data, 'block'),
            entrance: self::extractString($data, 'entrance'),
            floor: self::extractString($data, 'floor'),
            flatFiasId: self::extractString($data, 'flat_fias_id'),
            flatCadnum: self::extractString($data, 'flat_cadnum'),
            flatType: self::extractString($data, 'flat_type'),
            flatTypeFull: self::extractString($data, 'flat_type_full'),
            flat: self::extractString($data, 'flat'),
            flatArea: self::extractFloat($data, 'flat_area'),
            squareMeterPrice: self::extractFloat($data, 'square_meter_price'),
            flatPrice: self::extractFloat($data, 'flat_price'),
            roomFiasId: self::extractString($data, 'room_fias_id'),
            roomCadnum: self::extractString($data, 'room_cadnum'),
            roomType: self::extractString($data, 'room_type'),
            roomTypeFull: self::extractString($data, 'room_type_full'),
            room: self::extractString($data, 'room'),
            postalBox: self::extractString($data, 'postal_box'),
            fiasId: self::extractString($data, 'fias_id'),
            fiasCode: self::extractString($data, 'fias_code'),
            fiasLevel: self::extractString($data, 'fias_level'),
            fiasActualityState: self::extractString($data, 'fias_actuality_state'),
            kladrId: self::extractString($data, 'kladr_id'),
            geonameId: self::extractString($data, 'geoname_id'),
            capitalMarker: self::extractString($data, 'capital_marker'),
            okato: self::extractString($data, 'okato'),
            oktmo: self::extractString($data, 'oktmo'),
            taxOffice: self::extractString($data, 'tax_office'),
            taxOfficeLegal: self::extractString($data, 'tax_office_legal'),
            timezone: self::extractString($data, 'timezone'),
            geoLat: self::extractFloat($data, 'geo_lat'),
            geoLon: self::extractFloat($data, 'geo_lon'),
            beltwayHit: self::extractString($data, 'beltway_hit'),
            beltwayDistance: self::extractInt($data, 'beltway_distance'),
            metro: self::extractMetro($data),
            divisions: self::extractDivisions($data),
            qcGeo: self::extractString($data, 'qc_geo'),
            qcComplete: self::extractString($data, 'qc_complete'),
            qcHouse: self::extractString($data, 'qc_house'),
            historyValues: self::extractHistoryValues($data),
            unparsedParts: self::extractString($data, 'unparsed_parts'),
            source: self::extractString($data, 'source'),
            qc: self::extractString($data, 'qc'),
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function extractString(array $data, string $key): ?string
    {
        return isset($data[$key]) && is_string($data[$key]) ? $data[$key] : null;
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function extractInt(array $data, string $key): ?int
    {
        if (!isset($data[$key])) {
            return null;
        }

        return is_int($data[$key]) ? $data[$key] : (is_numeric($data[$key]) ? (int) $data[$key] : null);
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function extractFloat(array $data, string $key): ?float
    {
        if (!isset($data[$key])) {
            return null;
        }

        return is_float($data[$key]) || is_int($data[$key]) || is_string($data[$key]) ? (float) $data[$key] : null;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return list<MetroStationDto>|null
     */
    private static function extractMetro(array $data): ?array
    {
        if (!isset($data['metro']) || !is_array($data['metro'])) {
            return null;
        }

        $metro = [];
        foreach ($data['metro'] as $station) {
            if (is_array($station)) {
                /** @var array<string, mixed> $station */
                $metro[] = MetroStationDto::fromArray($station);
            }
        }

        return $metro !== [] ? $metro : null;
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function extractDivisions(array $data): ?DivisionsDto
    {
        if (!isset($data['divisions']) || !is_array($data['divisions'])) {
            return null;
        }

        /** @var array<string, mixed> $divisionsData */
        $divisionsData = $data['divisions'];

        return DivisionsDto::fromArray($divisionsData);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return list<string>|null
     */
    private static function extractHistoryValues(array $data): ?array
    {
        if (!isset($data['history_values']) || !is_array($data['history_values'])) {
            return null;
        }

        $historyValues = [];
        foreach ($data['history_values'] as $value) {
            if (is_string($value)) {
                $historyValues[] = $value;
            }
        }

        return $historyValues !== [] ? $historyValues : null;
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function extractArray(array $data, string $key): mixed
    {
        return isset($data[$key]) && is_array($data[$key]) ? $data[$key] : null;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'postal_code'             => $this->postalCode,
            'country'                 => $this->country,
            'country_iso_code'        => $this->countryIsoCode,
            'federal_district'        => $this->federalDistrict,
            'region_fias_id'          => $this->regionFiasId,
            'region_kladr_id'         => $this->regionKladrId,
            'region_iso_code'         => $this->regionIsoCode,
            'region_with_type'        => $this->regionWithType,
            'region_type'             => $this->regionType,
            'region_type_full'        => $this->regionTypeFull,
            'region'                  => $this->region,
            'area_fias_id'            => $this->areaFiasId,
            'area_kladr_id'           => $this->areaKladrId,
            'area_with_type'          => $this->areaWithType,
            'area_type'               => $this->areaType,
            'area_type_full'          => $this->areaTypeFull,
            'area'                    => $this->area,
            'sub_area_fias_id'        => $this->subAreaFiasId,
            'sub_area_kladr_id'       => $this->subAreaKladrId,
            'sub_area_with_type'      => $this->subAreaWithType,
            'sub_area_type'           => $this->subAreaType,
            'sub_area_type_full'      => $this->subAreaTypeFull,
            'sub_area'                => $this->subArea,
            'city_fias_id'            => $this->cityFiasId,
            'city_kladr_id'           => $this->cityKladrId,
            'city_with_type'          => $this->cityWithType,
            'city_type'               => $this->cityType,
            'city_type_full'          => $this->cityTypeFull,
            'city'                    => $this->city,
            'city_area'               => $this->cityArea,
            'city_district_fias_id'   => $this->cityDistrictFiasId,
            'city_district_kladr_id'  => $this->cityDistrictKladrId,
            'city_district_with_type' => $this->cityDistrictWithType,
            'city_district_type'      => $this->cityDistrictType,
            'city_district_type_full' => $this->cityDistrictTypeFull,
            'city_district'           => $this->cityDistrict,
            'settlement_fias_id'      => $this->settlementFiasId,
            'settlement_kladr_id'     => $this->settlementKladrId,
            'settlement_with_type'    => $this->settlementWithType,
            'settlement_type'         => $this->settlementType,
            'settlement_type_full'    => $this->settlementTypeFull,
            'settlement'              => $this->settlement,
            'street_fias_id'          => $this->streetFiasId,
            'street_kladr_id'         => $this->streetKladrId,
            'street_with_type'        => $this->streetWithType,
            'street_type'             => $this->streetType,
            'street_type_full'        => $this->streetTypeFull,
            'street'                  => $this->street,
            'stead_fias_id'           => $this->steadFiasId,
            'stead_kladr_id'          => $this->steadKladrId,
            'stead_cadnum'            => $this->steadCadnum,
            'stead_type'              => $this->steadType,
            'stead_type_full'         => $this->steadTypeFull,
            'stead'                   => $this->stead,
            'house_fias_id'           => $this->houseFiasId,
            'house_kladr_id'          => $this->houseKladrId,
            'house_cadnum'            => $this->houseCadnum,
            'house_flat_count'        => $this->houseFlatCount,
            'house_type'              => $this->houseType,
            'house_type_full'         => $this->houseTypeFull,
            'house'                   => $this->house,
            'block_type'              => $this->blockType,
            'block_type_full'         => $this->blockTypeFull,
            'block'                   => $this->block,
            'entrance'                => $this->entrance,
            'floor'                   => $this->floor,
            'flat_fias_id'            => $this->flatFiasId,
            'flat_cadnum'             => $this->flatCadnum,
            'flat_type'               => $this->flatType,
            'flat_type_full'          => $this->flatTypeFull,
            'flat'                    => $this->flat,
            'flat_area'               => $this->flatArea,
            'square_meter_price'      => $this->squareMeterPrice,
            'flat_price'              => $this->flatPrice,
            'room_fias_id'            => $this->roomFiasId,
            'room_cadnum'             => $this->roomCadnum,
            'room_type'               => $this->roomType,
            'room_type_full'          => $this->roomTypeFull,
            'room'                    => $this->room,
            'postal_box'              => $this->postalBox,
            'fias_id'                 => $this->fiasId,
            'fias_code'               => $this->fiasCode,
            'fias_level'              => $this->fiasLevel,
            'fias_actuality_state'    => $this->fiasActualityState,
            'kladr_id'                => $this->kladrId,
            'geoname_id'              => $this->geonameId,
            'capital_marker'          => $this->capitalMarker,
            'okato'                   => $this->okato,
            'oktmo'                   => $this->oktmo,
            'tax_office'              => $this->taxOffice,
            'tax_office_legal'        => $this->taxOfficeLegal,
            'timezone'                => $this->timezone,
            'geo_lat'                 => $this->geoLat,
            'geo_lon'                 => $this->geoLon,
            'beltway_hit'             => $this->beltwayHit,
            'beltway_distance'        => $this->beltwayDistance,
            'metro'                   => $this->metro !== null ? array_map(fn (MetroStationDto $station) => $station->toArray(), $this->metro) : null,
            'divisions'               => $this->divisions?->toArray(),
            'qc_geo'                  => $this->qcGeo,
            'qc_complete'             => $this->qcComplete,
            'qc_house'                => $this->qcHouse,
            'history_values'          => $this->historyValues,
            'unparsed_parts'          => $this->unparsedParts,
            'source'                  => $this->source,
            'qc'                      => $this->qc,
        ];
    }
}

<?php

// England: ceremonial counties · Wales: preserved counties · Scotland: council areas · Northern Ireland: counties.
$nations = [
    'England' => ['Bedfordshire', 'Berkshire', 'Bristol', 'Buckinghamshire', 'Cambridgeshire', 'Cheshire', 'City of London', 'Cornwall',
        'Cumbria', 'Derbyshire', 'Devon', 'Dorset', 'Durham', 'East Riding of Yorkshire', 'East Sussex', 'Essex', 'Gloucestershire',
        'Greater London', 'Greater Manchester', 'Hampshire', 'Herefordshire', 'Hertfordshire', 'Isle of Wight', 'Kent', 'Lancashire',
        'Leicestershire', 'Lincolnshire', 'Merseyside', 'Norfolk', 'North Yorkshire', 'Northamptonshire', 'Northumberland',
        'Nottinghamshire', 'Oxfordshire', 'Rutland', 'Shropshire', 'Somerset', 'South Yorkshire', 'Staffordshire', 'Suffolk', 'Surrey',
        'Tyne and Wear', 'Warwickshire', 'West Midlands', 'West Sussex', 'West Yorkshire', 'Wiltshire', 'Worcestershire'],
    'Wales' => ['Clwyd', 'Dyfed', 'Gwent', 'Gwynedd', 'Mid Glamorgan', 'Powys', 'South Glamorgan', 'West Glamorgan'],
    'Scotland' => ['Aberdeen City', 'Aberdeenshire', 'Angus', 'Argyll and Bute', 'City of Edinburgh', 'Clackmannanshire',
        'Dumfries and Galloway', 'Dundee City', 'East Ayrshire', 'East Dunbartonshire', 'East Lothian', 'East Renfrewshire', 'Falkirk',
        'Fife', 'Glasgow City', 'Highland', 'Inverclyde', 'Midlothian', 'Moray', 'Na h-Eileanan Siar', 'North Ayrshire',
        'North Lanarkshire', 'Orkney Islands', 'Perth and Kinross', 'Renfrewshire', 'Scottish Borders', 'Shetland Islands',
        'South Ayrshire', 'South Lanarkshire', 'Stirling', 'West Dunbartonshire', 'West Lothian'],
    'Northern Ireland' => ['Antrim', 'Armagh', 'Down', 'Fermanagh', 'Londonderry', 'Tyrone'],
];

$regions = [];
foreach ($nations as $nation => $counties) {
    foreach ($counties as $county) {
        $label = $county.' ('.$nation.')';
        $regions[$label] = $label;
    }
}

return $regions;

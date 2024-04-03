<?php

namespace blizko\LibrenmsAPIPlugin\Http\Controllers;

use Illuminate\Routing\Controller;

class APIController extends Controller
{
    function get_device_port_by_mac(Illuminate\Http\Request $request) {
        $mac_address = $request->route('mac_address');
                $q = //'select distinct fdb.port_id, fdb.mac_address, fdb.device_id, d.sysName, p.ifName from'.
                        'select distinct fdb.mac_address, d.sysName, p.ifName from '.
                        'ports_fdb as fdb,'.
                        'devices as d,'.
                        'ports as p '.
                        'where fdb.port_id not in (select local_port_id from links) and '.
            'fdb.port_id not in (select remote_port_id from links where remote_port_id is not null) and '.
                        'fdb.device_id=d.device_id and '.
                        'fdb.port_id = p.port_id and '.
                        //'fdb.mac_address REGEXP "^16bf05*" and '.
        //			'fdb.updated_at > now()-interval 48 hour and '.
            'p.ifType = "ethernetCsmacd" and '.
                        'fdb.mac_address = ? '.
            'order by fdb.updated_at DESC limit 1;';
        $data = dbFetchRows($q, [$mac_address]);
        return response()->json($data, 200, [], JSON_PRETTY_PRINT);
    }
    
    function get_device_port_by_device_id(Illuminate\Http\Request $request) {
        $device_group_id = $request->route('device_group_id');
        /*
        $q = 'select distinct fdb.mac_address, d.sysName, p.ifName from '.
                'ports_fdb as fdb, '.
                'devices as d, '.
                'ports as p, '.
                'device_groups as dgr, '.
                'device_group_device as dgrd '.
                'where fdb.port_id not in (select local_port_id from links) and '.
                'fdb.port_id not in (select remote_port_id from links where remote_port_id is not null) and '.
                'fdb.device_id = d.device_id and '.
                'fdb.port_id = p.port_id and '.
                'fdb.updated_at > DATE_SUB(NOW(), INTERVAL 24 HOUR) and '.
                'p.ifType = "ethernetCsmacd" and '.
                //'fdb.mac_address REGEXP "^16bf05*" and '.
                'dgrd.device_id = d.device_id and '.
                'dgrd.device_group_id = dgr.id and '.
                'dgr.id = ?;';
        $data = dbFetchRows($q, [$device_group_id]);
        */
        
        // Query for any Group ID (bypass)
        $q = 'select distinct fdb.mac_address, d.sysName, p.ifName from '.
        'ports_fdb as fdb, '.
        'devices as d, '.
        'ports as p '.
        //'device_groups as dgr, '.
        //'device_group_device as dgrd '.
        'where fdb.port_id not in (select local_port_id from links) '.
        'and fdb.port_id not in (select remote_port_id from links where remote_port_id is not null) '.
        'and fdb.port_id not in (select port_id from ports_fdb group by port_id having count(*) > 4) '. // HACK. Added this filter to exclude ports with multiple Macs
        'and fdb.device_id = d.device_id '.
        'and fdb.port_id = p.port_id '.
        'and fdb.updated_at > DATE_SUB(NOW(), INTERVAL 24 HOUR) '.
        'and p.ifType = "ethernetCsmacd" ';
        //'and fdb.mac_address REGEXP "^16bf05*" '.
        //'and dgrd.device_id = d.device_id '.
        //'and dgrd.device_group_id = dgr.id '.
        //'and dgr.id = ?;';
        $data = dbFetchRows($q, []);
        return response()->json($data, 200, [], JSON_PRETTY_PRINT);
    }
    
    function get_device_by_physaddress(Illuminate\Http\Request $request){
        $physaddress = $request->route('physaddress');
        $q = 'select distinct d.device_id, d.hostname from devices as d '.
        'left join ports as p on p.device_id = d.device_id '.
        'where '.
        'p.ifPhysAddress = ? ;';
        $data = dbFetchRows($q, [$physaddress]);
        return response()->json($data, 200, [], JSON_PRETTY_PRINT);
    }
    
    function get_device_by_physaddress_raw(Illuminate\Http\Request $request){
        $physaddress = $request->route('physaddress');
        $q = 'select distinct d.hostname from devices as d '.
        'left join ports as p on p.device_id = d.device_id '.
        'where '.
        'p.ifPhysAddress = ? ;';
        $data = dbFetchRows($q, [$physaddress]);
        return response()->json($data, 200, [], JSON_PRETTY_PRINT);
}

CloudBase Plugin
================

CloudBase is a plugin for Wordpress to provide features for managing a flying club, specifically
a glider club operation. CloudBase is the core module and it is expected plugins dependent on
these core functions will also be developed. A key feature of CloudBase is the addition of 
many RESTfull endpoints to aid in the development of those additional features. Wordpress 
provides the core features of user management although a plugin such as "Members" is highly
recommended.  

### Roles
Cloud Base relies on Wordpress Roles. And add several of it's own: Treasurer, Tow Pilot, CFI-G, Operations, Chief Tow Pilot, Chief CFI-G, Inactive and Board Member. These Roles 
control who has access to various functions in the Plugin and give authority for the various Sign offs. 

CloudBase Admin features
------------------------

Once CloudBase is activated a new sub-menu, called "Cloud Base" will be available in the "Settings" Menu. Several Tabs are 
available under this submenu. The first that needs to be configured is the "Basic Configuration Page" on this page you will need
to enter your flying club name, a short or nick name, set you altitude units (meters/feet), Fiscal Year, and Start of your flying Season. 
Under the "Equipment Types" tab you will need to enter the types of equipment you wish to keep track off. Tow Planes and Gliders are 
pre-configured. Using ShortCodes you can creat pages where you may track Registration, Annual and Transponder due dates of the Equipment
entered here. Under the "Status Types" tab you can enter possible statues for your equipement. Available and Grounded are preconfigured. 
You may also set a color code. This color will be used in the Status Shortcode to display the Competition ID to give a quick display of 
available and grounded aircraft. The "Flights Types" tab allow you to set up different types of flights. "Regular" is preconfigured, you
might want to add "AOF" or instruction, etc. 

With the above set up you can star to enter the Actual aircraft and equipement your club uses under the "Equipment" tab. Glider and Tow plane
types require, Registration, Make and Model. (Registration, Annual, and Transponder Due dates will be entered using the status Shortcode, see below.)

The final tab "Sign Off Types" is where you enter "Sign offs" for your members. A Sign off might be "Dues Paid", "Annual flight Review", "Checked out to fly XYZ" etc. 
You will need to enter a title, who is the authority to sign off, (CFI-G, Tresurer, etc), effective period, and if not having the sign off is a reason to be on the "No fly" list. 
It can also be marked as being applied automatically to everyone. 

CloudBase Shortcodes
--------------------

CloudBase currently has two Short Codes: "display_flights", and "display_status". "display_status" displays a short one or two line list of all of the 
status of aircraft(tow plane or glider) listed in the Admin Equipment tab by Competition ID. The "Display_Status" shortcode accepts one option; details="true". When this 
option is used the shortcode will display a table of all equipment, this table includes: Registration, Competition ID, Model, Status, Annual due, Registration Due, Transponder Due, 
comments. If the loged in user has admin rights this table will be editable and updatable. 

Cloudbase REST Endpoints
------------------------

### RESTfull endpoints provided by CloudBase are: 

wordpress/wp-json/cloud_base/v1/aircraft_types/
wordpress/wp-json/cloud_base/v1/aircraft/
wordpress/wp-json/cloud_base/v1/fees/
wordpress/wp-json/cloud_base/v1/flight_types/
wordpress/wp-json/cloud_base/v1/flights/
wordpress/wp-json/cloud_base/v1/pilots/
wordpress/wp-json/cloud_base/v1/sign_off_types/
wordpress/wp-json/cloud_base/v1/sign_off/squawks/
wordpress/wp-json/cloud_base/v1/aircraft_status/


### This is version 1.0 of the interface.

### /pilots
Pliot related information. Pilot data is primarally managed from Wordpress Dashboard. Therefore only GET is implemented here. 
#### GET

If no paramater returns all flying members. If optional role is set will return a list of those pilots. 

* Optional paramater: role= 
	* valid values: 'cfi_g', 'tow_pilot', 'subscriber', 'CFI_G', 'TOW_PILOT', 'SUBSCRIBER'

### /pilots/data
Pilot data - this is information supplied by the pilot about themselves. Address Phone number etc.
#### GET
Pilot ID requied, returns all data for given pilot. Will include sign offs that can be self updated. 
#### PUT
Pilot ID required any of the pilot parameters supplied will be updated. ('last\_name', 'first\_name', 'address1', 'address2', 'city', 'state', 'soaringsociety','zip', 'cel', 'tel', 'wrk', 'pvtgliderinsco' , ' pvtinspolicynum', 'contact1name', 'contact1relationship',  'contact1cel',  'contact1tel',  'contact1address', 'contact1city', 'contact1state',  'contact1zip', 'contact2name',  'contact2relationship', 'contact2cel', 'contact2tel', 'contact2address', 'contact2city',   'contact2state', 'contact2zip', 'certificate' , 'cirtissuedate', 'certType', 'endorsements', 'totalhours', 'gliderflights', badge', 'pvtgldmake', 'pvtgldmodel','pvtnnumber', 'pvtcompnum' ,'elt' , 'transponder', 'pvtinsurexpdate', 'pvtinspolicynum')

Can also update effect date for self sign off items. (hmm could be multiple how to handle....) 

### /fees
Fees and charges, primaraly tow fees. 
####GET
Returns a list of current fees.

#### POST
Add a new fee requires aliltude, cost, and optional hood\up fee. Hook\_up is zero if not supplied. 

####  PUT
Required :fee Id, updated cost and or hook\_up fee. 

#### Delete
Not implemented, probably a good idea to leave it that way. 

### /aircraft
Aircraft related information
#### GET
With no parameters returns a summarized list of all aircraft includes the aircraft ID, registration number, competition did, status and type.

* Recognizes two filters, type and Captian. ?type= or ?Captian= may be combined.
* Also recognizes “AUDIT. If audit=1 is included will return history of changes to selected aircraft.
* If an ID is supplies will return details of that aircraft.

#### POST
Add a new aircraft required parameters are:

* type - must already exist
* registration
* make
* model
* Optional parameters
	* Captian - must be an active pilot
 	* Status - must be an established status (see below)
	* Competition ID

#### PATCH
Update aircraft record aircraft id must be supplied
* Optional
	* Type - new type
	* Captian - new Captian
	* Status
	* 
#### DELETE
Delete an aircraft aircraft Id must  be supplied.

### /status
Avaliable status's of aircraft, grounded, squawks etc. Made it a seperate endpoint because who knows what statues people will come up with. 
####GET
* If no Id is supplied a list of all status and Ids will be returned
* If an ID is supplied that status will be returned.

#### POST
* Status must be supplied - Id will be generated. Will check for duplicates

#### PUT 
* Id and status are required updates the status field

#### DELETE
* Id required, checks to see if any aircraft are using the status, if the status is in use an error is returned.

### /aircraft_types
####GET
Returns a list of aircraft type and ids

#### POST

type must be supplied - Id will be generated. Will check for duplicates

#### PUT 

id must be supplied, type will be updated. 
     
#### DELETE

Id required, checks to see if any aircraft are using the type, if the type is in use an error is returned. Otherwise type will be deleted. 

### flight_types
#### GET
Returns a list of flight types and ids

#### POST

type must be supplied - Id will be generated. Will check for duplicates

#### PUT 

id must be supplied, type will be updated. 
     
#### DELETE

Id required, checks to see if any aircraft are using the type, if the type is in use an error is returned. Otherwise type will be deleted. 


### /squawks

#### GET
- list summary of all open squawks.
- if ID supplied, return details of squawk.
- audit=1 returns all squawks 
- filters: registration\_no, make, model, type

#### POST
Create a new squawk, requies aircraft ID or compitition Id. Discritpion of issue. Aircraft must exist. 

#### PUT 

Update squawk, must include squawk id. 

#### DELETE

Must include squawk Id. actually does not delete marks complete and archives. []([]([]([]())))
    
### /flights
The whole point of this API to be able to record flights. 
####GET
If no parameter is supplied will return all of todays flights. Valid parameters are:

* flight\_id
* date
* daterange
* filters
	* member\_id
	* instrutor\_id
	* glider\_id
	* towpilot\_id
	* towplane\_id
	* flight|-type

#### POST
* Create a new flight. Must have aircraft ID. 
* Optional parameters:
	* 	Member\_id
	*  flight\_type
	*  Instructor\_id
	*  TowPilot\_id
	*  tug\_id
	*  take\_off\_datetime
	*  landing\_datetime
	*  notes

Returns new flight\_id.	

#### PUT 

flight\_id required. May update any POST parameter. 


### /sign_offs
Manage pilot sign offs. 

#### GET
Pilot Id required, returns a list of all of the pilots sign offs. 

#### POST
Add a user sign off. Requires Pilot Id, Sign of type Id, Effective date. Authority will be current logged in user. (assuming curent user is authorized.)  

#### PUT

Update a user sign off. Sign off Id, Effective date. Authority will be current logged in user. (assuming curent user is authorized.)  

#### DELETE

Delete a user sign off. Sign off Id, Authority will be current logged in user. (assuming curent user is authorized.)  

### /sign_off_types
Types of sign offs avaliable. 
#### GET
With no parameters will return a list of all sign offs. If an ID is supplies will return a details of a specific sign off. 
#### POST
Create a new sign off. 
Required parameters are:

* signoff_type
* period (biennial, yearly, monthly, quarterly, no_expire, fixed, Yearly-EOM, Biennial-EOM)
* fixed_date (if "fixed" is selected)
* Authority (CFI-G, Tresurer, Tow_pilot, Chief\_CFI\_G, Chief\_tow, Operations, self, god)
* Optional parameters:
	* 	No_fly, if present sign off becomes a no-fly requirement.
	*  all, applied to all pilots

#### PUT

* Required: signoff\_id
	* Optional : any of the POST parameters. 



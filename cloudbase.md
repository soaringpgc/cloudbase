#Cloudbase REST Endpoints 

CloudBase is a plugin for Wordpress. All authentication is provided by Wordpress.
Basic user interface is performed thru Wordpress or the Wordpress REST api
CloudBase is an extension of the Wordpress API ie: wprd[ress/wp-json/cloudbase/v1/...

##This is version 1.0 of the interface.

###/pilots
Pliot related information. Pilot data is primarally managed from Wordpress Dashboard. Therefore only GET is implemented here. 
####GET

If no paramater returns all flying members. If optional role is set will return a list of those pilots. 

* Optional paramater: role= 
	* valid values: 'cfi\_g', 'tow\_pilot', 'subscriber', 'CFI\_G', 'TOW\_PILOT', 'SUBSCRIBER'

###/pilots/data
Pilot data - this is information supplied by the pilot about themselves. Address Phone number etc.
####GET
Pilot ID requied, returns all data for given pilot. Will include sign offs that can be self updated. 
####PATCH/PUT
Pilot ID required any of the pilot parameters supplied will be updated. ('last\_name', 'first\_name', 'address1', 'address2', 'city', 'state', 'soaringsociety','zip', 'cel', 'tel', 'wrk', 'pvtgliderinsco' , ' pvtinspolicynum', 'contact1name', 'contact1relationship',  'contact1cel',  'contact1tel',  'contact1address', 'contact1city', 'contact1state',  'contact1zip', 'contact2name',  'contact2relationship', 'contact2cel', 'contact2tel', 'contact2address', 'contact2city',   'contact2state', 'contact2zip', 'certificate' , 'cirtissuedate', 'certType', 'endorsements', 'totalhours', 'gliderflights', badge', 'pvtgldmake', 'pvtgldmodel','pvtnnumber', 'pvtcompnum' ,'elt' , 'transponder', 'pvtinsurexpdate', 'pvtinspolicynum')

Can also update effect date for self sign off items. (hmm could be multiple how to handle....) 

###/fees
Fees and charges, primaraly tow fees. 
####GET
Returns a list of current fees.

####POST
Add a new fee requires aliltude, cost, and optional hood\up fee. Hook\_up is zero if not supplied. 

####PATCH/PUT
Required :fee Id, updated cost and or hook\_up fee. 

####Delete
Not implemented, probably a good idea to leave it that way. 

### /aircraft
Aircraft related information
#### GET
With no parameters returns a summarized list of all aircraft includes the aircraft ID, registration number, competition did, status and type.

* Recognizes two filters, type and Captian. ?type= or ?Captian= may be combined.
* Also recognizes “AUDIT. If audit=1 is included will return history of changes to selected aircraft.
* If an ID is supplies will return details of that aircraft.

####POST
Add a new aircraft required parameters are:

* type - must already exist
* registration
* make
* model
* Optional parameters
	* Captian - must be an active pilot
 	* Status - must be an established status (see below)
	* Competition ID

####PATCH
Update aircraft record aircraft id must be supplied
* Optional
	* Type - new type
	* Captian - new Captian
	* Status
	* 
####DELETE
Delete an aircraft aircraft Id must  be supplied.

###/status
Avaliable status's of aircraft, grounded, squawks etc. Made it a seperate endpoint because who knows what statues people will come up with. 
####GET
* If no Id is supplied a list of all status and Ids will be returned
* If an ID is supplied that status will be returned.

####POST
* Status must be supplied - Id will be generated. Will check for duplicates

####PUT/PATCH
* Id and status are required updates the status field

####DELETE
* Id required, checks to see if any aircraft are using the status, if the status is in use an error is returned.

###/aircraft_types
####GET
Returns a list of aircraft type and ids

####POST

type must be supplied - Id will be generated. Will check for duplicates

####PUT/PATCH

id must be supplied, type will be updated. 
     
####DELETE

Id required, checks to see if any aircraft are using the type, if the type is in use an error is returned. Otherwise type will be deleted. 

###/flight_types
####GET
Returns a list of flight types and ids

####POST

type must be supplied - Id will be generated. Will check for duplicates

####PUT/PATCH

id must be supplied, type will be updated. 
     
####DELETE

Id required, checks to see if any aircraft are using the type, if the type is in use an error is returned. Otherwise type will be deleted. 


###/squawks

####GET
- list summary of all open squawks.
- if ID supplied, return details of squawk.
- audit=1 returns all squawks 
- filters: registration\_no, make, model, type

####POST
Create a new squawk, requies aircraft ID or compitition Id. Discritpion of issue. Aircraft must exist. 

####PUT/PATCH

Update squawk, must include squawk id. 

####DELETE

Must include squawk Id. actually does not delete marks complete and archives. []([]([]([]())))
    
###/flights
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

####POST
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

####PUT/PATCH

flight\_id required. May update any POST parameter. 


###/sign_offs
Manage pilot sign offs. 

####GET
Pilot Id required, returns a list of all of the pilots sign offs. 

####POST
Add a user sign off. Requires Pilot Id, Sign of type Id, Effective date. Authority will be current logged in user. (assuming curent user is authorized.)  

####PATCH/PUT

Update a user sign off. Sign off Id, Effective date. Authority will be current logged in user. (assuming curent user is authorized.)  

####DELETE

Delete a user sign off. Sign off Id, Authority will be current logged in user. (assuming curent user is authorized.)  

###/sign_off_types
Types of sign offs avaliable. 
####GET
With no parameters will return a list of all sign offs. If an ID is supplies will return a details of a specific sign off. 
####POST
Create a new sign off. 
Required parameters are:

* signoff_type
* period (biennial, yearly, monthly, quarterly, no_expire, fixed, Yearly-EOM, Biennial-EOM)
* fixed_date (if "fixed" is selected)
* Authority (CFI-G, Tresurer, Tow_pilot, Chief\_CFI\_G, Chief\_tow, Operations, self, god)
* Optional parameters:
	* 	No_fly, if present sign off becomes a no-fly requirement.
	*  all, applied to all pilots

####PATCH/PUT

* Required: signoff\_id
	* Optional : any of the POST parameters. 



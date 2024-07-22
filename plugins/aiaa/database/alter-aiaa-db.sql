ALTER TABLE appraisals
ADD hasKeys BOOLEAN,
ADD runningStatus VARCHAR(255),
ADD additionalInformation TEXT,
ADD vehicleType VARCHAR(255);

ALTER TABLE appraisals
MODIFY COLUMN diminishedValueEstimate decimal(10,2) DEFAULT NULL,
MODIFY COLUMN appraisedValue decimal(10,2) DEFAULT NULL,
MODIFY COLUMN overview TEXT,
MODIFY COLUMN exteriorAppraisal1 TEXT DEFAULT NULL,
MODIFY COLUMN exteriorAppraisal2 TEXT DEFAULT NULL,
MODIFY COLUMN exteriorAppraisal3 TEXT DEFAULT NULL,
MODIFY COLUMN exteriorAppraisal4 TEXT DEFAULT NULL,
MODIFY COLUMN exteriorAppraisal5 TEXT DEFAULT NULL,
MODIFY COLUMN exteriorAppraisalSummary TEXT DEFAULT NULL,
MODIFY COLUMN interiorAppraisalInstruments TEXT DEFAULT NULL,
MODIFY COLUMN interiorAppraisalInstrumentsNotes TEXT DEFAULT NULL,
MODIFY COLUMN interiorAppraisalUpholstery TEXT DEFAULT NULL,
MODIFY COLUMN interiorAppraisalTrim TEXT DEFAULT NULL,
MODIFY COLUMN interiorAppraisalCarpets TEXT DEFAULT NULL,
MODIFY COLUMN interiorAppraisalAdditionalNotes TEXT DEFAULT NULL,
MODIFY COLUMN disclaimer TEXT DEFAULT NULL,
MODIFY COLUMN approvedBy bigint unsigned null;
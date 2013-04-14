# Project TODO Board
Lacking an issue management tool, I'll do this the old fashioned way: with a txt file.


# Project Components and Acceptance Criteria
- X API
  - X implemented as RESTful API
  - X game may post a user's score, intended for leaderboard rank reporting
  - X Assumption: game is running as a Facebook app, implying signed_request protocol
  - X API post payload = signed_request and user's score

- Database
  - X Contains user score, indexed by user's unique ID extracted from facebook request
  - X Test script to generate 1M records of test data

- Report
  - X Must answer "How many total players are there?"
  - X Must answer "How many people played the game today?"
  - Must "List the top ten players by score"
  - Must "List the top 10 players who improved their score over the week, assuming week ends on Sunday at Midnight"


# Additional Goals (not part of the official specification)
- Style the report


# Notes
I'll take notes here as I go along, to document questions or assumptions I make.
- the "1M rows of sample data"; there's no guidance on how many unique users that is.  I'll shoot for something like 50 records per user, average, so about 20,000 users.
- Need to make sure the test data spans at least 1 week, to capture the "improvement over last week" feature.
- Decoded example signed message didn't have any guidance around score format.  Not sure if I'm supposed to invent the API myself, I guess that's what's happening.
- Assuming MySQL here, and I'm not worrying about optimizing a DBAL
- pay attention to table indexes, I haven't yet optimzed them




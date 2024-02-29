# Backend from ByteBuddy Bot
[Link to ByteBuddy Bot](https://github.com/luka-lta/bytebuddy_bot)

This API is a simple backend to store the ByteBuddy Discord Bot data. 

## Endpoints
- [PUT /api/v1/register?guildId={guildId}](#put-apiv1registerguildidguildid) - Register a new Guild
- [GET /api/v1/guild?guildId={guildId}](#get-apiv1guildguildidguildid) - Get the guild data

## PUT /api/v1/register?guildId={guildId}
Body:
- serverName: string | **required**
- themeColor: string | **optional**

Response:
````json
{
  "success": true,
  "message": "Guild successfully registered",
  "statusCode": 200
}
````

## GET /api/v1/guild?guildId={guildId}
Response:
````json
{
  "success": true,
  "message": "Config data fetched successfully",
  "statusCode": 200,
    "data": {
        "guildId": "1234567890",
        "serverName": "Servername",
        "themeColor": "#000000"
    }
}
````
/**
 *  @OA\Schema(
 *      schema="error",
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *          enum={"error"}
 *      ),
 *      @OA\Property(
 *          property="code",
 *          type="integer",
 *          description="announcement ID"
 *      ),
 *      @OA\Property(
 *          property="status",
 *          type="string",
 *          description="Error Status"
 *      ),
 *      @OA\Property(
 *          property="message",
 *          type="string",
 *          description="Error Message"
 *      )
 *  )
 **/

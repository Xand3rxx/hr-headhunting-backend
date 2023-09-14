import { Controller, Get } from '@nestjs/common';
import { AppService } from '../service/app.service';

@Controller()
export class AppController {
  constructor(private readonly service: AppService) {}

  @Get('api/user')
  getUser(): string {
    return this.service.getUser();
  }

  @Get()
  getHello(): string {
    return this.service.getHello();
  }
}

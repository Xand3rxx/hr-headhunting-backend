import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { User } from 'src/entity/user.entity';
import { Repository } from 'typeorm';

@Injectable()
export class AppService {
  constructor(
    @InjectRepository(User)
    private readonly userRepository: Repository<User>,
  ) {}
  getHello(): string {
    return 'Hello World!';
  }

  getUser(): string {
    // Build user
    const auth = new User();
    auth.password = '1234';
    auth.name = 'Joboy';
    auth.orgId = 1;
    auth.createdBy = 0;

    auth.phoneCode = '234';
    auth.phoneNumber = '08399300';
    auth.type = 'admin';
    this.userRepository.save(auth).then((user) => {
      console.log({ user });
    });

    return 'Api user from service!';
  }
}

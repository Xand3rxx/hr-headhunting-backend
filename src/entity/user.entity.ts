import { Column, Entity } from 'typeorm';
import { Exclude } from 'class-transformer';
import { IsEmail, IsString } from 'class-validator';
import { AbstractEntity } from './abstract.entity';

@Entity()
export class User extends AbstractEntity {
  @Column()
  public name: string;

  // @IsEmail()
  // @Column({ nullable: true })
  // public email: string;
  @IsString()
  @Column()
  public phoneCode: string;

  @IsString()
  @Column()
  public phoneNumber: string;

  @IsString()
  @Column()
  @Exclude()
  public password: string;

  @Column()
  public createdBy: number;

  @Column()
  public orgId: number;

  @IsString()
  @Column()
  public type: string;

  @Column({
    default: false,
  })
  verified: boolean;
}
